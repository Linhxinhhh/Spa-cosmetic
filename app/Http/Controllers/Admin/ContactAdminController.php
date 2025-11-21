<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\ContactReply;
use App\Mail\ContactReplyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ContactAdminController extends Controller
{
  public function index(Request $request)
{
    $q      = trim((string) $request->q);
    $status = $request->status;

    // per_page linh hoạt (5–100), mặc định 20
    $perPage = (int) $request->input('per_page', 20);
    $perPage = max(5, min($perPage, 100));

    $contacts = Contact::query()
        ->when($q, function($qr) use ($q) {
            $qr->where(fn($x) => $x
                ->where('name', 'like', "%$q%")
                ->orWhere('phone', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->orWhere('message', 'like', "%$q%"));
        })
        ->when(in_array($status, ['open','processing','done']), fn($qr) => $qr->where('status',$status))
        ->latest('created_at')
        ->paginate($perPage)
        ->withQueryString(); // giữ q, status, per_page trên link phân trang

    return view('dashboard.contacts.index', compact('contacts','q','status','perPage'));
}


    public function show(Contact $contact)
    {
        $contact->load('replies.admin');
        return view('dashboard.contacts.show', compact('contact'));
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate(['status' => 'required|in:open,processing,done']);
        $contact->status = $request->status;
        if ($request->status === 'done' && !$contact->responded_at) {
            $contact->responded_at = now();
        }
        $contact->save();

        return back()->with('success','Đã cập nhật trạng thái.');
    }
    private function mapSubcategoryFromSubject(?string $subject): ?string
{
    $s = Str::lower(trim((string)$subject));
    return match (true) {
        str_contains($s,'da')   => 'Chăm sóc da',
        str_contains($s,'tóc')  => 'Chăm sóc tóc',
        str_contains($s,'cơ thể')   => 'Chăm sóc cơ thể',
        str_contains($s,'spa')  => 'Dịch vụ spa',
        default => null,
    };
}
    private function upsertFaqFromContact(Contact $contact, string $answer = '', bool $publish = true): Faq
{
    return Faq::updateOrCreate(
        ['contact_id' => $contact->contact_id], // nếu đã có thì update
        [
            'question'   => trim($contact->message ?: $contact->subject ?: 'Câu hỏi'),
            'answer'     => $answer,
            'category'   => $this->subjectToCategory($contact->subject),
            'subcategory' => $this->mapSubcategoryFromSubject($contact->subject),
            'sort_order' => 0,
            'status'     => $publish ? Faq::STATUS_PUBLISHED : Faq::STATUS_DRAFT,
            'views'      => 0,
        ]
    );
}
private function subjectToCategory(?string $s): string
{
    $s = trim((string) $s);
    if ($s === '') return 'Khác';

    // map các biến thể nếu cần
    $map = [
        'hoi dap'          => 'Hỏi đáp',
        'tu van dich vu'   => 'Tư vấn dịch vụ',
        'dat lich'         => 'Đặt lịch',
    ];
    $key = Str::slug($s, ' '); // "Tư vấn dịch vụ" -> "tu van dich vu"
    return $map[$key] ?? Str::title($s); // fallback giữ nguyên subject đã chuẩn hoá
}

    /** Admin trả lời */
public function reply(Request $request, Contact $contact)
{
    $data = $request->validate([
        'message'        => 'required|string|min:2',
        'via'            => 'required|in:note,email',
        'email_subject'  => 'nullable|string|max:190',
        'publish_to_faq' => 'nullable'
    ]);

    // Xác định kênh gửi thực tế trước khi lưu reply
    $via = $data['via'];
    $shouldEmail = ($via === 'email' && filled($contact->email));
    if ($via === 'email' && blank($contact->email)) {
        $via = 'note'; // fallback
    }

    $reply = null;

    // Gộp các thao tác DB
    DB::beginTransaction();
    try {
        // 1) Lưu reply
        $reply = ContactReply::create([
            'contact_id' => $contact->contact_id,
            'admin_id'   => auth()->id(),
            'via'        => $via,
            'message'    => $data['message'],
        ]);

        // 2) Cập nhật trạng thái liên hệ
        if ($shouldEmail) {
            $contact->status = 'done';
            $contact->responded_at = $contact->responded_at ?: now();
        } else {
            if ($contact->status === 'open') {
                $contact->status = 'processing';
            }
        }
        $contact->save();

        // 3) (Tuỳ chọn) Đưa vào FAQ — question = message (yêu cầu của bạn)
        if ($request->boolean('publish_to_faq')) {
            // Lưu liên kết contact_id để truy vết
            $this->upsertFaqFromContact($contact, $data['message'], true);
        }

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Contact reply failed', ['contact_id'=>$contact->contact_id, 'error'=>$e->getMessage()]);
        return back()->with('error', 'Không thể lưu phản hồi: '.$e->getMessage());
    }

    // 4) Gửi email SAU khi commit
    if ($shouldEmail) {
        try {
            $subject = $data['email_subject']
                ?: ($contact->subject ? ('Phản hồi: '.$contact->subject) : ('Phản hồi liên hệ #'.$contact->contact_id));

            Mail::to($contact->email)
                ->bcc(config('mail.from.address'))
                ->send(new ContactReplyMail($contact, $reply, $subject));

            return back()->with('success', 'Đã lưu phản hồi và gửi email cho khách.');
        } catch (\Throwable $e) {
            Log::error('Contact reply email FAILED', [
                'to'=>$contact->email, 'contact_id'=>$contact->contact_id, 'error'=>$e->getMessage()
            ]);
            // DB đã lưu, chỉ báo lỗi phần email
            return back()->with('success', 'Đã lưu phản hồi. Gửi email thất bại: '.$e->getMessage());
        }
    }

    return back()->with('success', 'Đã thêm ghi chú.');
}



    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('success','Đã xoá liên hệ.');
    }
}

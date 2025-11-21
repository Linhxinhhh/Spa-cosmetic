<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FaqAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $statusInput = $request->status;

        // Chuẩn hoá status: chấp nhận cả EN & VI, lọc theo VI (trùng với store/update)
        $statusMap = [
            'draft'     => 'Bản nháp',
            'published' => 'Xuất bản',
            'Bản nháp'  => 'Bản nháp',
            'Xuất bản'  => 'Xuất bản',
        ];
        $statusFilter = $statusMap[$statusInput] ?? null;

        $faqs = Faq::query()
            ->when($statusFilter, fn ($qr) => $qr->where('status', $statusFilter))
            ->when($q, fn ($qr) => $qr->search($q))
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // Trả lại nguyên $statusInput để giữ selected trên UI
        return view('dashboard.faq.index', [
            'faqs'   => $faqs,
            'q'      => $q,
            'status' => $statusInput,
        ]);
    }

    public function create()
    {
        return view('dashboard.faq.form', ['faq' => new Faq]);
    }

    public function edit(Faq $faq)
    {
        return view('dashboard.faq.form', compact('faq'));
    }

    public function store(Request $r)
    {
        $nextOrder = Faq::where('category', $r->input('category'))
              ->max('sort_order');
        $data = $r->validate([
            'question'   => 'required|string|max:255',
            'answer'     => 'required|string',
            'category'   => 'nullable|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'status'      => ['required', Rule::in(['Xuất bản', 'Bản nháp'])],
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);
        if ($r->hasFile('cover')) {
                $data['cover_image'] = $r->file('cover')->store('faqs', 'public');
            }

        Faq::create($data);

        $data['sort_order'] = $data['sort_order'] ?? $nextOrder;
        return redirect()->route('admin.faqs.index')->with('success', 'Đã tạo FAQ.');
    }

    public function update(Request $r, Faq $faq)
    {
        $data = $r->validate([
            'question'   => 'required|string|max:255',
            'answer'     => 'required|string',
            'category'   => 'nullable|string|max:100',
            'subcategory' => 'nullable|string|max:100',

            'sort_order' => 'nullable|integer|min:0',
            'status'      => ['required', Rule::in(['Xuất bản', 'Bản nháp'])],
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);
        // xoá ảnh cũ nếu check xoá hoặc upload ảnh mới
        if ($r->boolean('remove_cover') && $faq->cover_image) {
            Storage::disk('public')->delete($faq->cover_image);
            $data['cover_image'] = null;
        }

        if ($r->hasFile('cover')) {
            if ($faq->cover_image) {
                Storage::disk('public')->delete($faq->cover_image);
            }
            $data['cover_image'] = $r->file('cover')->store('faqs','public');
        }
        $faq->update($data);
        return redirect()->route('admin.faqs.index')->with('success', 'Đã cập nhật FAQ.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'Đã xoá FAQ.');
    }

    public function toggle(Faq $faq)
    {
        // Hỗ trợ cả dữ liệu cũ (EN) lẫn mới (VI)
        $isPublished = in_array($faq->status, ['published', 'Xuất bản'], true);

        $faq->status = $isPublished ? 'Bản nháp' : 'Xuất bản';
        $faq->save();

        return back()->with('success', $isPublished ? 'Đã chuyển nháp.' : 'Đã xuất bản.');
    }
}

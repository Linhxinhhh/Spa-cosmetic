<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\TreatmentSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerSessionController extends Controller
{
    // danh sách các buổi của khách (tương lai)
    public function index()
    {
        $userId = Auth::id();

        $customer = Customer::where('user_id', $userId)->first();

        if (!$customer) {
            $sessions = collect();
        } else {
           $sessions = TreatmentSession::with([
                'plan.packageService',
                'plan.singleService',
            ])
            ->whereHas('plan', function ($q) use ($customer) {
                $q->where('customer_id', $customer->id ?? $customer->customer_id);
            })
            // CHỈ HIỆN BUỔI CHƯA XONG
            ->whereIn('status', ['scheduled', 'confirmed'])
            
            ->where('scheduled_start', '>=', now()->subDay())
            ->orderBy('scheduled_start')
            ->paginate(20);
        }

        return view('Users.sessions.index', compact('sessions'));
    }

    // form dời lịch 1 buổi
    public function edit(TreatmentSession $session)
    {
        $this->authorizeSession($session);

        return view('Users.sessions.edit', compact('session'));
    }

    // lưu giờ mới
    public function update(Request $r, TreatmentSession $session)
    {
        $this->authorizeSession($session);

        // ví dụ: không cho dời khi còn < 2 giờ
        if ($session->scheduled_start && $session->scheduled_start->lessThan(now()->addHours(2))) {
            return back()->with('error', 'Buổi này sắp diễn ra, không thể dời lịch online. Vui lòng liên hệ spa.');
        }

        $data = $r->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        // giữ nguyên duration cũ
        $duration = $session->scheduled_end && $session->scheduled_start
            ? $session->scheduled_end->diffInMinutes($session->scheduled_start)
            : 60;

        $newStart = Carbon::parse($data['date'].' '.$data['time']);
        $newEnd   = $newStart->copy()->addMinutes($duration);

        // check trùng với buổi khác của khách (cùng KH)
        $conflict = TreatmentSession::where('id', '<>', $session->id)
            ->whereHas('plan', function ($q) use ($session) {
                $q->where('customer_id', $session->plan->customer_id);
            })
            ->where('scheduled_start', '<', $newEnd)
            ->where('scheduled_end', '>', $newStart)
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Khung giờ này trùng với buổi khác của bạn, vui lòng chọn giờ khác.');
        }

        $session->update([
            'scheduled_start' => $newStart,
            'scheduled_end'   => $newEnd,
            'status'          => 'scheduled',   // hoặc giữ nguyên
        ]);

        return redirect()
            ->route('users.customer.sessions.index')
            ->with('success', 'Đã dời lịch buổi điều trị.');
    }

    // hủy 1 buổi
    public function cancel(Request $r, TreatmentSession $session)
    {
        $this->authorizeSession($session);

        // ví dụ: không cho hủy khi còn < 2 giờ
        if ($session->scheduled_start && $session->scheduled_start->lessThan(now()->addHours(2))) {
            return back()->with('error', 'Buổi này sắp diễn ra, không thể hủy online. Vui lòng liên hệ spa.');
        }

        $reason = $r->input('reason');

        $session->update([
            'status' => 'canceled',
            'note'   => trim(($session->note ? $session->note."\n" : '').'KH hủy: '.$reason),
        ]);

        return redirect()
            ->route('users.customer.sessions.index')
            ->with('success', 'Đã hủy buổi điều trị.');
    }

    // kiểm tra buổi có thuộc khách đang login không
    protected function authorizeSession(TreatmentSession $session)
    {
        $userId = Auth::id();
        $customer = Customer::where('user_id', $userId)->first();

        if (!$customer || (int)$session->plan->customer_id !== (int)($customer->id ?? $customer->customer_id)) {
            abort(403);
        }
    }
}

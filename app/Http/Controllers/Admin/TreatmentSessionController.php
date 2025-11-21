<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TreatmentSession;
use App\Models\TreatmentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TreatmentScheduleMail;
use App\Mail\SessionStatusUpdated; // thêm mailable mới
use Carbon\Carbon;

class TreatmentSessionController extends Controller
{
    // DANH SÁCH BUỔI
    public function index(Request $r)
    {
        $q = TreatmentSession::with([
                'plan.customer',
                'plan.packageService',
                'plan.singleService',
                'packageStep',
            ])
            ->orderByDesc('scheduled_start');

        if ($r->filled('date')) {
            $q->whereDate('scheduled_start', $r->date);
        }

        if ($r->filled('status')) {
            $q->where('status', $r->status);
        }

        $sessions = $q->paginate(30);

        return view('dashboard.tsessions.index', compact('sessions'));
    }

    // FORM SỬA
    public function edit(TreatmentSession $session)
    {
        return view('dashboard.tsessions.edit', compact('session'));
    }

    // CẬP NHẬT
    public function update(Request $r, TreatmentSession $session)
    {
        $data = $r->validate([
            'scheduled_start' => 'required|date',
            'scheduled_end'   => 'required|date|after:scheduled_start',
            'staff_id'        => 'nullable|integer',
            'room_id'         => 'nullable|integer',
            'status'          => 'required|string',
            'note'            => 'nullable|string',
        ]);

        // Lưu lại trạng thái cũ
        $oldStatus = $session->status;

        // Cập nhật
        $session->update($data);

        // Lấy lại kế hoạch và khách hàng
        $plan = TreatmentPlan::with(['customer','packageService','singleService','sessions'])
            ->find($session->treatment_plan_id);

        if ($plan && $plan->customer && $plan->customer->email) {
            try {
                // Nếu chỉ muốn gửi mail khi trạng thái thay đổi
                if ($oldStatus !== $session->status) {
                    // Gửi mail thông báo thay đổi trạng thái
                    Mail::to($plan->customer->email)->send(
                        new SessionStatusUpdated($session->fresh(), $oldStatus, $session->status)
                    );
                }

                // Nếu muốn gửi lại mail toàn bộ lịch trình mỗi lần có update:
                // Mail::to($plan->customer->email)->send(new TreatmentScheduleMail($plan));

            } catch (\Throwable $e) {
                \Log::error('Lỗi gửi mail cập nhật buổi điều trị: '.$e->getMessage());
            }
        }

        return redirect()
            ->route('admin.treatment-plans.show', $session->treatment_plan_id)
            ->with('success', 'Đã cập nhật buổi và gửi email thông báo cho khách hàng (nếu có).');
    }

    // XOÁ BUỔI
    public function destroy(TreatmentSession $session)
    {
        $planId = $session->treatment_plan_id;
        $session->delete();

        return redirect()
            ->route('admin.treatment-plans.show', $planId)
            ->with('success', 'Đã xóa buổi.');
    }
}

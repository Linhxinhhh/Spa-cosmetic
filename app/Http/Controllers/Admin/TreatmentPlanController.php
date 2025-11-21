<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TreatmentPlan;
use App\Models\TreatmentSession;
use App\Models\Service;
use App\Models\Customer; // đổi lại nếu tên khác
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;

use App\Mail\TreatmentScheduleMail; 

class TreatmentPlanController extends Controller
{
    // DANH SÁCH (admin)
   public function index(Request $request)
{
    $query = TreatmentPlan::with([
        'customer',
        'packageService',
        'singleService',
    ])->withCount('sessions');

    // ============================
    // Lọc TRẠNG THÁI
    // ============================
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // ============================
    // Lọc LOẠI DỊCH VỤ
    // ============================
    if ($request->filled('type')) {
        if ($request->type === 'single') {
            $query->whereNotNull('single_service_id');
        }
        if ($request->type === 'package') {
            $query->whereNotNull('package_service_id');
        }
    }

    // ============================
    // TÌM KIẾM: khách, dịch vụ, gói, ID
    // ============================
    if ($request->filled('q')) {
        $keyword = $request->q;

        $query->where(function ($q) use ($keyword) {
            $q->whereHas('customer', function ($c) use ($keyword) {
                $c->where('name', 'LIKE', "%$keyword%");
            })
            ->orWhereHas('singleService', function ($s) use ($keyword) {
                $s->where('service_name', 'LIKE', "%$keyword%");
            })
            ->orWhereHas('packageService', function ($p) use ($keyword) {
                $p->where('service_name', 'LIKE', "%$keyword%");
            })
            ->orWhere('id', $keyword);
        });
    }

    $plans = $query->orderByDesc('id')
                   ->paginate(20)
                   ->withQueryString();

    return view('dashboard.treatment_plans.index', compact('plans'));
}

    // FORM TẠO
    public function create()
    {
        $customers = Customer::orderBy('name')->get(); // hoặc ->orderBy('customer_name')
        $singleServices = Service::where('type', 'Lẻ')->orderBy('service_name')->get();
        $packageServices = Service::where('type', 'Gói')->orderBy('service_name')->get();

        return view('dashboard.treatment_plans.create', compact('customers', 'singleServices', 'packageServices'));
    }

    // AJAX: sinh lịch nháp
    public function preview(Request $request)
    {
        $data = $request->validate([
            'customer_id'        => 'required|integer',
            'start_date'         => 'required|date',
            'single_service_id'  => 'nullable|integer',
            'package_service_id' => 'nullable|integer',
            'preferred_dow'      => 'array',
            'preferred_time_range' => 'array',
        ]);

        if (empty($data['single_service_id']) && empty($data['package_service_id'])) {
            return response()->json(['message' => 'Chọn dịch vụ hoặc gói'], 422);
        }

        // mặc định khung giờ
        $timeRange = $data['preferred_time_range'] ?? ['09:00', '20:00'];
        $dow       = $data['preferred_dow'] ?? [1,2,3,4,5,6,0];
        $startDate = Carbon::parse($data['start_date']);

        $sessions = [];

        // 1) Nếu là dịch vụ Lẻ => 1 buổi
        if (!empty($data['single_service_id'])) {
            $service = Service::findOrFail($data['single_service_id']);
            $dur = $service->duration ?? 60;
            $start = $startDate->copy()->setTimeFromTimeString($timeRange[0]);
            $end   = $start->copy()->addMinutes($dur);

            $sessions[] = [
                'session_no' => 1,
                'scheduled_start' => $start->toDateTimeString(),
                'scheduled_end'   => $end->toDateTimeString(),
                'status' => 'draft',
                'staff_id' => null,
                'room_id'  => null,
            ];
        }

        // 2) Nếu là Gói => lấy cấu hình từ service_package_meta + service_package_steps
        if (!empty($data['package_service_id'])) {
            $pkgId = $data['package_service_id'];

            $meta = DB::table('service_package_meta')
                ->where('package_service_id', $pkgId)
                ->first();

            // nếu chưa có meta thì coi như 1 buổi 60p
            $total = $meta->total_sessions ?? 1;
            $minGap = $meta->min_gap_days ?? 0;
            $maxGap = $meta->max_gap_days ?? 10;
            $dur    = $meta->default_duration_min ?? 60;

            $steps = DB::table('service_package_steps')
                ->where('package_service_id', $pkgId)
                ->orderBy('step_no')
                ->get();

            if ($steps->isEmpty()) {
                // tự tạo theo total
                $steps = collect(range(1, $total))->map(fn($i) => (object)[
                    'id' => null,
                    'step_no' => $i,
                    'duration_min' => null,
                    'min_gap_days' => null,
                    'max_gap_days' => null,
                ]);
            }

            $cursor = $startDate->copy();

            $i = 0;
            foreach ($steps as $step) {
                $i++;
                $duration = $step->duration_min ?? $dur;
                $gapMin   = $step->min_gap_days ?? $minGap;

                if ($i === 1) {
                    // buổi đầu
                    $slotDay = $this->findNextDow($cursor, $dow);
                } else {
                    $cursor = $cursor->copy()->addDays($gapMin);
                    $slotDay = $this->findNextDow($cursor, $dow);
                }

                $start = $slotDay->copy()->setTimeFromTimeString($timeRange[0]);
                $end   = $start->copy()->addMinutes($duration);

                $sessions[] = [
                    'session_no' => $i,
                    'package_step_id' => $step->id,
                    'scheduled_start' => $start->toDateTimeString(),
                    'scheduled_end'   => $end->toDateTimeString(),
                    'status' => 'draft',
                    'staff_id' => null,
                    'room_id'  => null,
                ];

                $cursor = $slotDay->copy(); // làm mốc cho buổi sau
            }
        }

        return response()->json([
            'plan' => $data,
            'sessions' => $sessions,
        ]);
    }

    // LƯU KẾ HOẠCH & BUỔI
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'        => 'required|integer',
            'start_date'         => 'required|date',
            'single_service_id'  => 'nullable|integer',
            'package_service_id' => 'nullable|integer',
            'preferred_dow'      => 'array',
            'preferred_time_range' => 'array',
            'sessions'           => 'required|array|min:1',
            'sessions.*.scheduled_start' => 'required|date',
            'sessions.*.scheduled_end'   => 'required|date|after:sessions.*.scheduled_start',
        ]);

        $planId = DB::transaction(function () use ($data) {
            $plan = TreatmentPlan::create([
                'customer_id'        => $data['customer_id'],
                'start_date'         => $data['start_date'],
                'single_service_id'  => $data['single_service_id'] ?? null,
                'package_service_id' => $data['package_service_id'] ?? null,
                'preferred_dow'      => $data['preferred_dow'] ?? [],
                'preferred_time_range' => $data['preferred_time_range'] ?? [],
                'status'             => 'active',
            ]);

            foreach ($data['sessions'] as $i => $s) {
                TreatmentSession::create([
                    'treatment_plan_id' => $plan->id,
                    'session_no'        => $i + 1,
                    'package_step_id'   => $s['package_step_id'] ?? null,
                    'scheduled_start'   => $s['scheduled_start'],
                    'scheduled_end'     => $s['scheduled_end'],
                    'status'            => 'confirmed',
                ]);
            }

            return $plan->id;
        });
        $plan = TreatmentPlan::with(['customer','packageService','singleService','sessions'])
            ->find($planId);

        if ($plan && $plan->customer && $plan->customer->email) {
            Mail::to($plan->customer->email)->send(new TreatmentScheduleMail($plan));
        }

        // trả về dạng json cho JS
        return response()->json([
            'success'  => true,
            'redirect' => route('admin.treatment-plans.show', $planId),
        ]);
    }

    // XEM CHI TIẾT
    public function show(TreatmentPlan $plan)
    {
        $plan->load([
            'customer',
            'packageService',
            'singleService',
            'sessions.packageStep',
        ]);

        return view('dashboard.treatment_plans.show', compact('plan'));
    }

    // hàm nhỏ tìm ngày kế tiếp thuộc danh sách thứ
    private function findNextDow(Carbon $from, array $dows): Carbon
    {
        $cand = $from->copy();
        $allowed = collect($dows);
        for ($i = 0; $i < 14; $i++) {
            if ($allowed->contains($cand->dayOfWeek)) {
                return $cand;
            }
            $cand->addDay();
        }
        return $from;
    }
// ============================
// FORM EDIT (Chỉ sửa trạng thái)
// ============================
public function edit(TreatmentPlan $plan)
{
    $plan->load([
        'customer',
        'packageService',
        'singleService',
        'sessions'
    ]);

    return view('dashboard.treatment_plans.edit', compact('plan'));
}

// ============================
// UPDATE TRẠNG THÁI KẾ HOẠCH
// ============================
public function update(Request $request, TreatmentPlan $plan)
{
    $data = $request->validate([
        'status' => 'required|string|in:draft,active,scheduled,confirmed,completed,canceled,expired'
    ]);

    $plan->update([
        'status' => $data['status']
    ]);

    return redirect()
        ->route('admin.treatment-plans.index', $plan->id)
        ->with('success', 'Cập nhật trạng thái kế hoạch thành công!');
}



}

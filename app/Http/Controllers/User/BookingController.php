<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\TreatmentPlan;
use App\Models\TreatmentSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\TreatmentScheduleMail;

class BookingController extends Controller
{
    public function __construct()
    {
        // Bắt buộc đăng nhập mới được vào create/store
        $this->middleware('auth');
    }

    /**
     * Trang đặt lịch (frontend)
     */
public function index()
{
    $appointments = Appointment::where('user_id', auth()->id())
        ->whereHas('service', function($q) {
            $q->where('type', '!=', 'Gói');   // Chỉ lấy dịch vụ lẻ
        })
        ->orderByDesc('appointment_date')
        ->paginate(10);

    return view('Users.booking.index', compact('appointments'));
}

    public function create(Request $request, ?Service $service = null)
    {
        $services = Service::select('service_id', 'service_name', 'duration', 'type', 'slug')
            ->orderBy('service_name')
            ->get();

        // dịch vụ được chọn trước
        $selectedServiceId = old('service_id');

        if (!$selectedServiceId) {
            if ($service) {
                $selectedServiceId = $service->service_id;
            } elseif ($slug = $request->query('service')) {
                $selectedServiceId = Service::where('slug', $slug)->value('service_id');
            } elseif ($sid = $request->query('service_id')) {
                $selectedServiceId = $sid;
            }
        }

        // user đang đăng nhập
        $authUser = Auth::user();

        // lấy customer theo user_id (nếu có)
        $customer = null;
        if ($authUser) {
            $customer = Customer::where('user_id', $authUser->user_id ?? $authUser->id)->first();
        }

        return view('Users.booking.create', [
            'services'          => $services,
            'selectedServiceId' => $selectedServiceId,
            'authUser'          => $authUser,
            'customer'          => $customer,
        ]);
    }

    /**
     * Lưu đặt lịch
     */
    public function store(Request $request)
    {
        $authUser = Auth::user();

        // base rules
        $rules = [
            'service_id'       => 'required|exists:services,service_id',
            'appointment_date' => 'required|date|after_or_equal:today',  // FIX: Thêm after_or_equal:today để tránh đặt quá khứ
            'start_time'       => 'required|date_format:H:i',
            'notes'            => 'nullable|string',
        ];

        // (trong case này đã bắt buộc auth, nên thực tế sẽ không vào đây nữa,
        // nhưng mình giữ cho “an toàn” nếu sau này bạn cho guest đặt)
        if (!$authUser) {
            $rules['full_name'] = 'required|string|max:255';
            $rules['phone']     = 'required|string|max:30';
            $rules['email']     = 'nullable|email';
        }

        $data = $request->validate($rules);

        // lấy dịch vụ
        $service = Service::findOrFail($data['service_id']);

        // check trùng giờ trong appointments
        $dupe = Appointment::where('service_id', $service->service_id)
            ->where('appointment_date', $data['appointment_date'])
            ->where('start_time', $data['start_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($dupe) {
            return back()
                ->withInput()
                ->with('error', 'Khung giờ này đã có lịch hẹn, vui lòng chọn thời gian khác.');
        }

        // lấy thông tin KH từ user (nếu có) hoặc từ form
        $fullName = $data['full_name'] ?? $authUser?->name;
        $phone    = $data['phone']     ?? $authUser?->phone;
        $email    = $data['email']     ?? $authUser?->email;

        // tính end_time theo duration
        $endTime = null;
        if (!empty($service->duration)) {
            $endTime = Carbon::createFromFormat('H:i', $data['start_time'])
                ->addMinutes((int) $service->duration)
                ->format('H:i');
        }

        // sẽ dùng để gửi mail sau khi transaction xong
        $planId = null;

        DB::transaction(function () use (
            $authUser, $service, $data, $fullName, $phone, $email, $endTime, &$planId
        ) {
            // 1. tạo appointment
            Appointment::create([
                'user_id'          => $authUser?->user_id ?? $authUser?->id,
                'service_id'       => $service->service_id,
                'staff_id'         => null,
                'appointment_date' => $data['appointment_date'],
                'start_time'       => $data['start_time'],
                'end_time'         => $endTime,
                'status'           => 'pending',
                'notes'            => $data['notes'] ?? null,
                // nếu bảng appointments chưa có 3 cột này thì bỏ 3 dòng dưới
                'customer_name'    => $fullName,
                'customer_phone'   => $phone,
                'customer_email'   => $email,
            ]);

            // 2. NẾU LÀ GÓI → tạo luôn treatment_plans + treatment_sessions
            if ($service->type !== 'Gói') {
                return;
            }

            // 2.1 tìm hoặc tạo customer
            if ($authUser) {
                $customer = Customer::firstOrCreate(
                    ['user_id' => $authUser->user_id ?? $authUser->id],
                    ['name' => $fullName, 'phone' => $phone, 'email' => $email]
                );
            } else {
                $customer = Customer::create([
                    'name'  => $fullName,
                    'phone' => $phone,
                    'email' => $email,
                ]);
            }

            // 2.2 lấy cấu hình gói
            $meta = DB::table('service_package_meta')
                ->where('package_service_id', $service->service_id)
                ->first();

            $steps = DB::table('service_package_steps')
                ->where('package_service_id', $service->service_id)
                ->orderBy('step_no')
                ->get();

            // nếu chưa cấu hình steps → tạo tạm theo total_sessions
            if ($steps->isEmpty() && $meta?->total_sessions) {
                $tmp = collect();
                for ($i = 1; $i <= $meta->total_sessions; $i++) {
                    $tmp->push((object)[
                        'id'           => null,
                        'step_no'      => $i,
                        'duration_min' => null,
                        'min_gap_days' => null,
                        'max_gap_days' => null,
                    ]);
                }
                $steps = $tmp;
            }

            // 2.3 tạo plan
            $plan = TreatmentPlan::create([
                'customer_id'          => $customer->id ?? $customer->customer_id,
                'package_service_id'   => $service->service_id,
                'single_service_id'    => null,
                'start_date'           => $data['appointment_date'],
                'preferred_dow'        => [],
                'preferred_time_range' => [],
                'status'               => 'active',
                'note'                 => 'Tạo tự động từ đặt lịch frontend',
            ]);

            $planId = $plan->id;

            // 2.4 tạo các buổi
            $firstStart = Carbon::parse($data['appointment_date'] . ' ' . $data['start_time']);
            $defaultDur = $meta->default_duration_min ?? $service->duration ?? 60;
            $defaultGap = $meta->min_gap_days ?? 3;

            $cursor = $firstStart->copy();
            $i = 0;

            foreach ($steps as $step) {
                $i++;

                if ($i === 1) {
                    $start = $firstStart->copy();
                } else {
                    $gapDays = $step->min_gap_days ?? $defaultGap;
                    $start   = $cursor->copy()->addDays($gapDays);
                }

                $end = $start->copy()->addMinutes($step->duration_min ?? $defaultDur);

                TreatmentSession::create([
                    'treatment_plan_id' => $plan->id,
                    'session_no'        => $i,
                    'package_step_id'   => $step->id,
                    'scheduled_start'   => $start,
                    'scheduled_end'     => $end,
                    'status'            => 'scheduled',
                ]);

                $cursor = $start->copy();
            }
        });

        // 3. nếu là gói & đã tạo plan → gửi mail lịch trình cho khách
        if ($planId) {
            $plan = TreatmentPlan::with(['customer','packageService','singleService','sessions'])
                ->find($planId);

            if ($plan && $plan->customer && $plan->customer->email) {
                Mail::to($plan->customer->email)->send(new TreatmentScheduleMail($plan));
            }
        }

        return back()->with('success', 'Đặt lịch thành công! Nhân viên sẽ liên hệ để xác nhận.')->withInput();  // FIX: Thêm withInput() để giữ old values
    }

    /**
     * API lấy availability (khung giờ đã booked)
     * Dùng cho frontend JS để hiển thị slot "hết chỗ"
     */
    public function availability(Request $request)
    {
        $serviceId = $request->get('service_id');
        $date = $request->get('date');  // Format 'YYYY-MM-DD'

        if (!$serviceId || !$date) {
            return response()->json([]);  // Trả rỗng nếu thiếu param
        }

        // Query các start_time đã booked (pending hoặc confirmed, khớp với logic check dupe)
        // Format: 'HH:MM' (slice từ 'HH:MM:SS' nếu DB lưu full time)
        $bookedTimes = Appointment::where('service_id', $serviceId)
                                  ->where('appointment_date', $date)
                                  ->whereIn('status', ['pending', 'confirmed'])  // Khớp với store() check dupe
                                  ->distinct('start_time')
                                  ->pluck('start_time')
                                  ->map(function ($time) {
                                      return substr($time, 0, 5);  // '09:00:00' -> '09:00'
                                  })
                                  ->values()
                                  ->toArray();

        return response()->json($bookedTimes);  // Ví dụ: ["09:00", "10:30"]
    }
public function showAppointment($id)
{
    $appointment = Appointment::with(['service'])
        ->where('user_id', auth()->id())
        ->where('appointment_id', $id)
        ->firstOrFail();

    return view('Users.booking.show', compact('appointment'));
}
public function rescheduleAppointment(Request $request, $id)
{
    $request->validate([
        'appointment_date' => 'required|date',
        'start_time'       => 'required',
        'end_time'         => 'required',
    ]);

    $appointment = Appointment::where('appointment_id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    if ($appointment->status === 'completed') {
        return back()->with('error', 'Buổi đã hoàn thành, không thể thay đổi.');
    }

    $appointment->update([
        'appointment_date' => $request->appointment_date,
        'start_time'       => $request->start_time,
        'end_time'         => $request->end_time,
        'status'           => 'confirmed', // Hoặc scheduled tùy bạn
        'notes'            => 'Khách đã dời lịch qua website',
    ]);

    return back()->with('success', 'Bạn đã dời lịch hẹn thành công.');
}


} 
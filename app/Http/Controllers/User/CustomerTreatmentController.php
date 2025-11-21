<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TreatmentPlan;
use App\Models\TreatmentSession;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerTreatmentController extends Controller
{
    // danh sách kế hoạch của KH
    public function index()
    {
            $userId = Auth::id(); // bảng users.user_id

        $customer = Customer::where('user_id', $userId)->first();

        if (!$customer) {
            $plans = collect();
        } else {
            $plans = TreatmentPlan::with(['packageService','singleService'])
                ->where('customer_id', $customer->id ?? $customer->customer_id)
                ->orderByDesc('id')
                        ->paginate(10);
        }


        return view('Users.treatments.index', compact('plans'));
    }

    // chi tiết 1 kế hoạch
     public function show(TreatmentPlan $plan)
    {
        $userId = Auth::id();
        $customer = Customer::where('user_id', $userId)->first();

        // khách này không có customer / hoặc plan không thuộc về khách này
        if (!$customer || (int)$plan->customer_id !== (int)($customer->id ?? $customer->customer_id)) {
            abort(403);
        }

        $plan->load([
            'packageService',
            'singleService',
            'sessions.packageStep',
        ]);
        return view('Users.treatments.show', compact('plan'));
    }

    // tất cả buổi của KH (gộp từ nhiều kế hoạch) => tiện cho màn "Lịch hẹn của tôi"
    public function sessions()
    {
        $customerId = Auth::id();

        $sessions = TreatmentSession::query()
            ->select('treatment_sessions.*')
            ->join('treatment_plans', 'treatment_plans.id', '=', 'treatment_sessions.treatment_plan_id')
            ->where('treatment_plans.customer_id', $customerId)
            ->orderBy('scheduled_start')
            ->get();

        return view('Users.treatments.sessions', compact('sessions'));
    }
}

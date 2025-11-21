@php
    use Carbon\Carbon;

    $customer = $session->plan->customer ?? null;
    $serviceName = $session->plan->packageService->service_name
        ?? $session->plan->singleService->service_name
        ?? 'Dịch vụ';
    $start = $session->scheduled_start ? Carbon::parse($session->scheduled_start)->timezone('Asia/Ho_Chi_Minh') : null;
    $end   = $session->scheduled_end ? Carbon::parse($session->scheduled_end)->timezone('Asia/Ho_Chi_Minh') : null;

    $map = [
        'scheduled'  => 'Đã lên lịch',
        'confirmed'  => 'Đã xác nhận',
        'completed'  => 'Đã hoàn thành',
        'canceled'   => 'Đã hủy',
        'no_show'    => 'Không đến',
        'pending'    => 'Chờ xác nhận',
    ];
    $old = $map[$oldStatus] ?? $oldStatus;
    $new = $map[$newStatus] ?? $newStatus;
@endphp

@component('mail::message')
# Cập nhật trạng thái buổi điều trị

Xin chào {{ $customer->name ?? 'Quý khách' }},

Trạng thái buổi điều trị của bạn đã thay đổi:  
**{{ $old }} → {{ $new }}**

**Dịch vụ/ Gói:** {{ $serviceName }}

**Thời gian:**  
@isset($start)
- Bắt đầu: **{{ $start->format('d/m/Y H:i') }}**
@endisset
@isset($end)
- Kết thúc: **{{ $end->format('d/m/Y H:i') }}**
@endisset

**Số buổi trong liệu trình:** Buổi {{ $session->session_no }}@if($session->plan->sessions_count ?? false)/{{ $session->plan->sessions_count }}@endif

Nếu bạn cần thay đổi lịch, vui lòng phản hồi email này hoặc liên hệ hotline **090x xxx xxx**.

@component('mail::button', ['url' => route('users.treatments.show', $session->treatment_plan_id) ])
Xem liệu trình của tôi
@endcomponent

Trân trọng,  
**Lyn & Spa**
@endcomponent

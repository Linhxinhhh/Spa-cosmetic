<p>Chào {{ $plan->customer->name ?? 'Quý khách' }},</p>
<p>Spa đã tạo lịch trình cho bạn:</p>
<p><strong>Dịch vụ:</strong> {{ $plan->packageService->service_name ?? $plan->singleService->service_name ?? '' }}</p>
<ul>
@foreach($plan->sessions as $s)
    <li>Buổi {{ $s->session_no }}: {{ $s->scheduled_start?->format('d/m/Y H:i') }}</li>
@endforeach
</ul>
<p>
    Bạn có thể xem và dời/hủy các buổi tại:
    <a href="{{ route('users.customer.sessions.index') }}">
        Trang quản lý buổi điều trị
    </a>
    (bạn cần đăng nhập để thực hiện).
</p>
<p>Hẹn gặp bạn!</p>

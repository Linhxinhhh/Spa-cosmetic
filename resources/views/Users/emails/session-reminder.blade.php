<p>Chào {{ $session->plan->customer->name ?? 'Quý khách' }},</p>
<p>Nhắc bạn buổi spa:</p>
<p><strong>Buổi:</strong> {{ $session->session_no }}</p>
<p><strong>Thời gian:</strong> {{ $session->scheduled_start?->format('d/m/Y H:i') }}</p>
<p><strong>Dịch vụ:</strong> {{ $session->plan->packageService->service_name ?? $session->plan->singleService->service_name ?? '' }}</p>
<p>
    Bạn có thể xem và dời/hủy các buổi tại:
    <a href="{{ route('users.customer.sessions.index') }}">
        Trang quản lý buổi điều trị
    </a>
    (bạn cần đăng nhập để thực hiện).
</p>
<p>Chúc bạn một ngày tốt lành!</p>
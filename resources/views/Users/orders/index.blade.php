@extends('Users.layouts.home')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Đơn hàng của tôi</h2>
            <p class="text-muted">Quản lý và theo dõi đơn hàng của bạn</p>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-cart-x text-muted mb-3" viewBox="0 0 16 16">
                <path d="M7.354 5.646a.5.5 0 1 0-.708.708L7.793 7.5 6.646 8.646a.5.5 0 1 0 .708.708L8.5 8.207l1.146 1.147a.5.5 0 0 0 .708-.708L9.207 7.5l1.147-1.146a.5.5 0 0 0-.708-.708L8.5 6.793z"/>
                <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
            </svg>
            <h5 class="text-muted">Bạn chưa có đơn hàng nào</h5>
            <p class="text-muted">Hãy bắt đầu mua sắm để tạo đơn hàng đầu tiên!</p>
            <a href="{{ route('users.shop') }}" class="btn btn-primary mt-3">
                <i class="bi bi-shop"></i> Bắt đầu mua sắm
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($orders as $o)
            <div class="col-12">
                <div class="card shadow-sm border-0 hover-shadow">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-2 mb-3 mb-md-0">
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-receipt text-primary mb-2" viewBox="0 0 16 16">
                                        <path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z"/>
                                        <path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5"/>
                                    </svg>
                                    <div class="fw-bold small text-primary">{{ $o->order_code }}</div>
                                </div>
                            </div>
                            
                            <div class="col-md-2 mb-3 mb-md-0">
                                <div>
                                    <small class="text-muted d-block mb-1">Thanh toán</small>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-credit-card"></i> {{ $o->payment_method }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="col-md-2 mb-3 mb-md-0">
                                <div>
                                    <small class="text-muted d-block mb-1">Trạng thái</small>
                                    @php
                                        $statusColor = match($o->status) {
                                            'Đang xử lý' => 'warning',
                                            'Đã giao' => 'success',
                                            'Đã hủy' => 'danger',
                                            default => 'info'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ $o->status }}</span>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div>
                                    <small class="text-muted d-block mb-1">Tổng tiền</small>
                                    <h5 class="mb-0 text-danger fw-bold">{{ number_format($o->total_amount,0,',','.') }}₫</h5>
                                </div>
                            </div>
                            
                            <div class="col-md-2 mb-3 mb-md-0">
                                <div>
                                    <small class="text-muted d-block mb-1">Ngày đặt</small>
                                    <small class="fw-semibold">{{ $o->created_at->format('d/m/Y') }}</small>
                                    <small class="d-block text-muted">{{ $o->created_at->format('H:i') }}</small>
                                </div>
                            </div>
                            
                            <div class="col-md-1 text-end">
                                <a href="{{ route('users.orders.show', $o->order_id) }}" class="btn btn-primary btn-sm">
                                    Chi tiết <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination nếu cần -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
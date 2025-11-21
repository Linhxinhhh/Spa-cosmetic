@extends('Users.layouts.home')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.orders.index') }}">Đơn hàng của tôi</a></li>
            <li class="breadcrumb-item active">{{ $order->order_code }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold mb-2">Đơn hàng #{{ $order->order_code }}</h2>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar3"></i> Đặt ngày {{ $order->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            @php
                $statusColor = match($order->status) {
                    'Đang xử lý' => 'warning',
                    'Đã giao' => 'success',
                    'Đã hủy' => 'danger',
                    'Đang vận chuyển' => 'info',
                    default => 'secondary'
                };
            @endphp
            <span class="badge bg-{{ $statusColor }} fs-6 px-3 py-2">
                <i class="bi bi-truck"></i> {{ $order->status }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Thông tin đơn hàng -->
        <div class="col-lg-8">
            <!-- Sản phẩm -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-box-seam text-primary"></i> Sản phẩm đã đặt
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Sản phẩm</th>
                                    <th class="text-center">Đơn giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-center">Giảm giá</th>
                                    <th class="text-end pe-4">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="product-img me-3">
                                                @if($item->product)
                                                    <img src="{{ product_main_src($item->product) }}" loading="lazy"
                                                         alt="{{ $item->product->product_name ?? 'Sản phẩm' }}"
                                                         class="rounded"
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $item->product->product_name ?? 'Sản phẩm đã xóa' }}
                                                </div>
                                                @if($item->product && $item->product->sku)
                                                    <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted">{{ number_format($item->price, 0, ',', '.') }}₫</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">x{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->discount_percent > 0)
                                            <span class="badge bg-danger">-{{ $item->discount_percent }}%</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <strong class="text-primary">
                                            {{ number_format($item->quantity * $item->price, 0, ',', '.') }}₫
                                        </strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Không có sản phẩm nào trong đơn hàng này.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tổng tiền -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-muted">Tạm tính:</div>
                        </div>
                        <div class="col-6 text-end">
                            <strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
                        </div>
                        
                        <div class="col-6">
                            <div class="text-muted">Phí vận chuyển:</div>
                        </div>
                        <div class="col-6 text-end">
                            <strong class="text-success">Miễn phí</strong>
                        </div>
                        
                        <div class="col-12"><hr class="my-2"></div>
                        
                        <div class="col-6">
                            <h5 class="mb-0">Tổng cộng:</h5>
                        </div>
                        <div class="col-6 text-end">
                            <h4 class="mb-0 text-danger fw-bold">
                                {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Thông tin giao hàng -->
        <div class="col-lg-4">
            <!-- Thông tin thanh toán -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-credit-card text-success"></i> Thanh toán
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Phương thức</small>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-wallet2 text-primary me-2"></i>
                            <strong>{{ $order->payment_method }}</strong>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">Trạng thái thanh toán</small>
                        @php
                            $paymentColor = match($order->payment_status) {
                                'Đã thanh toán' => 'success',
                                'Chưa thanh toán' => 'warning',
                                'Thất bại' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $paymentColor }}">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Thông tin giao hàng -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-geo-alt text-danger"></i> Thông tin giao hàng
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Người nhận</small>
                        <strong>{{ $order->customer_name ?? 'Không có thông tin' }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Số điện thoại</small>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-telephone text-primary me-2"></i>
                            <strong>{{ $order->phone }}</strong>
                        </div>
                    </div>
                    
                    <div>
                        <small class="text-muted d-block mb-1">Địa chỉ giao hàng</small>
                        <div class="d-flex">
                            <i class="bi bi-pin-map text-danger me-2 mt-1"></i>
                            <span>{{ $order->shipping_address }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="d-grid gap-2 mt-4">
                <a href="{{ route('users.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
                @if($order->status == 'Đang xử lý')
                    <form method="POST" action="{{ route('users.orders.cancel', $order->id) }}" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Hủy đơn hàng
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: all 0.3s ease;
}

.table > tbody > tr {
    transition: background-color 0.2s ease;
}

.table > tbody > tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.product-img img,
.product-img div {
    border: 1px solid #e9ecef;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    text-color: #6c757d;
}

.badge {
    font-weight: 500;
}
</style>
@endsection
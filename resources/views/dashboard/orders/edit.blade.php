@extends('dashboard.layouts.app')

@section('breadcrumb-parent','Quản trị')
@section('breadcrumb-child','Đơn hàng')
@section('page-title','Cập nhật đơn hàng')

@section('content')

<style>
.page-header{
    background:linear-gradient(135deg,#1e40af 0%,#3b82f6 100%);
    border-radius:16px;padding:2rem;margin-bottom:2rem;color:#fff;position:relative;
}
.card-custom{
    border-radius:16px;
    border:1px solid #e5e7eb;
    padding:1.5rem;
    background:#fff;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
}
.status-badge{
    padding:6px 14px;border-radius:20px;font-weight:600;font-size:.85rem;
}
.badge-paid{background:#16a34a;color:#fff}
.badge-pending{background:#dbeafe;color:#1e40af}
.badge-failed{background:#ef4444;color:#fff}

.badge-processing{background:#3b82f6;color:#fff}
.badge-shipped{background:#1e40af;color:#fff}
.badge-delivered{background:#10b981;color:#fff}
.badge-cancelled{background:#ef4444;color:#fff}
.badge-refunded{background:#64748b;color:#fff}

.table-items th, .table-items td {
    padding: 0.75rem !important;
    vertical-align: middle;
}
</style>

@php
    $statusMap = $statusMap ?? \App\Models\Order::STATUS;
    $payMap    = $payMap    ?? \App\Models\Order::PAYMENT_STATUS;
@endphp

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-header">
        <h1 class="mb-1" style="font-size:2rem;font-weight:700;">
            <i class="fas fa-edit me-2"></i> Cập nhật trạng thái đơn hàng
        </h1>
        <div style="opacity:.9">#{{ $order->order_code ?? $order->order_id }}</div>
    </div>

    {{-- FORM UPDATE --}}
    <div class="card-custom mb-4">
        <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mb-3">
            @csrf
            @method('PUT')

            <h5 class="fw-bold mb-3"><i class="fas fa-shipping-fast me-2"></i>Trạng thái đơn hàng</h5>

            <div class="row">
                <div class="col-md-6">
                    <label class="fw-semibold mb-1">Trạng thái đơn hàng</label>
                    <select name="status" class="form-select py-2">
                        @foreach($statusMap as $key => $label)
                            <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="fw-semibold mb-1">Trạng thái thanh toán</label>
                    <form action="{{ route('admin.orders.updatePayment', $order) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <select name="payment_status" class="form-select py-2">
                            @foreach($payMap as $key => $label)
                                <option value="{{ $key }}" {{ $order->payment_status === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        <button class="btn btn-primary mt-3">
                            <i class="fas fa-save me-1"></i> Cập nhật thanh toán
                        </button>
                    </form>
                </div>
            </div>

            <button class="btn btn-success mt-4 px-4">
                <i class="fas fa-save me-2"></i> Lưu trạng thái đơn hàng
            </button>
        </form>
    </div>

    {{-- ORDER INFO --}}
    <div class="card-custom mb-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-user me-2"></i>Thông tin khách hàng</h5>

        <p class="mb-1"><strong>Khách hàng:</strong> {{ $order->user?->name ?? '—' }}</p>
        <p class="mb-1"><strong>Email:</strong> {{ $order->user?->email ?? '—' }}</p>
        <p class="mb-1"><strong>Số điện thoại:</strong> {{ $order->phone ?? $order->user?->phone ?? '—' }}</p>
        @if($order->shipping_address)
            <p class="mb-0"><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
        @endif
    </div>

    {{-- ORDER ITEMS --}}
    <div class="card-custom mb-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-box-open me-2"></i>Sản phẩm trong đơn</h5>

        <div class="table-responsive">
            <table class="table table-items align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tạm tính</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $it)
                        <tr>
                            <td>{{ $it->product?->product_name ?? '—' }}</td>
                            <td>{{ number_format($it->price,0,',','.') }}₫</td>
                            <td>{{ $it->quantity }}</td>
                            <td>{{ number_format($it->quantity * $it->price,0,',','.') }}₫</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- BACK BUTTON --}}
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary px-4">
        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
    </a>

</div>
@endsection

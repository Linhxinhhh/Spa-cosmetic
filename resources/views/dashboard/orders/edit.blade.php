@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Đơn hàng')
@section('page-title', 'Chỉnh sửa đơn hàng'  )

@push('styles')
<style>
    .create-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(30, 64, 175, 0.2);
    }

    .form-container {
        background: white;
        padding: 2.5rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .form-group { margin-bottom: 1.8rem; }

    .form-label {
        color: #1e40af;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control-modern {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .form-control-modern:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        background: white;
    }

    .form-control-modern.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    .btn-save {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        padding: 15px 40px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        min-width: 130px;
    }

    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(16, 185, 129, 0.4);
    }

    .btn-cancel {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        padding: 15px 40px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 100px;
    }

    .btn-cancel:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(107, 114, 128, 0.4);
        color: white;
    }

    /* Icon + Input/Select */
    .input-icon {
        position: relative;
    }
    .input-icon i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 1.2rem;
        z-index: 10;
    }
    .input-icon .form-control-modern,
    .input-icon select.form-control-modern {
        padding-left: 52px;
    }

    /* Icon + Textarea (icon nằm trên cùng) */
    .input-icon-start {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .input-icon-start i {
        font-size: 1.4rem;
        margin-top: 14px;
        color: #6b7280;
    }
    .input-icon-start textarea {
        flex: 1;
        min-height: 120px;
        resize: vertical;
    }

    .section-title {
        color: #1e40af;
        font-weight: 700;
        font-size: 1.4rem;
        margin-bottom: 1.8rem;
        padding-bottom: 0.8rem;
        border-bottom: 3px solid #dbeafe;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .summary-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        padding: 1.8rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 0;
        border-bottom: 1px dashed #cbd5e1;
    }

    .summary-item:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .price-final {
        font-size: 1.7rem;
        color: #dc2626;
        font-weight: 800;
    }

    .button-group {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 2px solid #f3f4f6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="create-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size: 2.6rem; font-weight: 800;">
                    <i class="fas fa-receipt me-3"></i>Chỉnh sửa đơn hàng
                </h1>
                <p class="mb-0 fs-5 opacity-90">
                    Mã đơn: <strong class="text-warning">#{{ $order->order_code ?? $order->id }}</strong>
                    &nbsp;&nbsp;|&nbsp;&nbsp;Khách hàng: <strong>{{ $order->user?->name ?? 'Khách lẻ' }}</strong>
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-cancel">
                    <i class="fas fa-arrow-left"></i> Quay lại chi tiết
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Form chỉnh sửa -->
        <div class="col-lg-8">
            <div class="form-container">
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Trạng thái -->
                    <div class="mb-5">
                        <h3 class="section-title">
                            <i class="fas fa-tasks"></i> Cập nhật trạng thái đơn hàng
                        </h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-truck text-primary"></i> Trạng thái đơn hàng
                                    </label>
                                    <div class="input-icon">
                                        <i class="fas fa-shipping-fast"></i>
                                        <select name="status" class="form-control-modern @error('status') is-invalid @enderror" required>
                                            @foreach(\App\Models\Order::STATUS as $key => $label)
                                                <option value="{{ $key }}" {{ old('status', $order->status) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-credit-card text-success"></i> Trạng thái thanh toán
                                    </label>
                                    <div class="input-icon">
                                        <i class="fas fa-money-check-alt"></i>
                                        <select name="payment_status" class="form-control-modern @error('payment_status') is-invalid @enderror" required>
                                            @foreach(\App\Models\Order::PAYMENT_STATUS as $key => $label)
                                                <option value="{{ $key }}" {{ old('payment_status', $order->payment_status) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Địa chỉ & Ghi chú -->
                    <div class="mb-5">
                        <h3 class="section-title">
                            <i class="fas fa-map-marked-alt"></i> Thông tin giao hàng & Ghi chú
                        </h3>

                        <!-- Địa chỉ giao hàng -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-map-location-dot text-danger"></i> Địa chỉ giao hàng
                            </label>
                            <div class="input-icon-start">
                                <i class="fas fa-home"></i>
                                <textarea name="shipping_address" rows="4"
                                          class="form-control-modern @error('shipping_address') is-invalid @enderror"
                                          placeholder="Nhập địa chỉ giao hàng đầy đủ...">{{ old('shipping_address', $order->shipping_address) }}</textarea>
                            </div>
                            @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                   
                    </div>

                    <!-- Nút hành động -->
                    <div class="button-group">
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-cancel">
                            <i class="fas fa-times"></i> Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>

      
    </div>
</div>
@endsection
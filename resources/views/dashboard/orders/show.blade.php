@extends('dashboard.layouts.app')
@section('breadcrumb-parent','Quản trị')
@section('breadcrumb-child','Đơn hàng')
@section('page-title','Chi tiết đơn #'.($order->order_code ?? $order->code ?? $order->id))

@section('content')
@php
  use Illuminate\Support\Str;

  // ===== Helpers =====
  $money = fn($n) => number_format((float)$n, 0, ',', '.').'₫';
  $toUrl = function (?string $p) {
      if (!$p) return null;
      if (Str::startsWith($p, ['http://','https://'])) return $p;
      $p = ltrim($p,'/');
      return Str::startsWith($p,'storage/') ? asset($p) : asset('storage/'.$p);
  };

  // Totals fallback
  $itemsTotal  = isset($itemsTotal)  ? (float)$itemsTotal  : (float)$order->items->sum('subtotal');
  $vatAmount   = isset($vatAmount)   ? (float)$vatAmount   : round($itemsTotal * 0.05);
  $grandTotal  = isset($grandTotal)  ? (float)$grandTotal  : ($itemsTotal + $vatAmount);

  // Map & badge
  $payMap = $payMap ?? [
      'pending' => 'Chờ thanh toán',
      'paid'    => 'Đã thanh toán',
      'failed'  => 'Thất bại',
  ];
  $statusMap = $statusMap ?? [
      'processing' => 'Đang xử lý',
      'shipped'    => 'Đã gửi',
      'delivered'  => 'Đã giao',
      'cancelled'  => 'Đã hủy',
      'refunded'   => 'Hoàn tiền',
      'pending'    => 'Chờ xử lý',
  ];

  $payClass = match($order->payment_status){
      'paid'   => 'badge-paid',
      'failed' => 'badge-failed',
      default  => 'badge-pending',
  };
  $payIcon = match($order->payment_status){
      'paid'   => 'bi-check-circle-fill',
      'failed' => 'bi-x-circle-fill',
      default  => 'bi-clock-fill',
  };
  $statusClass = match($order->status){
      'processing' => 'badge-processing',
      'shipped'    => 'badge-shipped',
      'delivered'  => 'badge-delivered',
      'cancelled'  => 'badge-cancelled',
      'refunded'   => 'badge-refunded',
      default      => 'badge-pending',
  };
  $statusIcon = match($order->status){
      'processing' => 'bi-arrow-repeat',
      'shipped'    => 'bi-truck',
      'delivered'  => 'bi-check-circle-fill',
      'cancelled'  => 'bi-x-circle-fill',
      'refunded'   => 'bi-arrow-counterclockwise',
      default      => 'bi-clock-fill',
  };

  // Customer info
  $user  = $order->user;
  $name  = $user?->name ?? '—';
  $phone = $order->phone ?: ($user?->phone ?? null);
  $email = $user?->email ?? null;
  $addr  = $order->shipping_address ?: ($user?->customer?->address ?? null);
@endphp

<style>
  /* ===== Variables & Base ===== */
  :root {
    --primary: #1e40af;
    --primary-dark: #1e3a8a;
    --primary-light: #3b82f6;
    --success: #10b981;
    --danger: #ef4444;
    --gray: #64748b;
    --gray-light: #f8fafc;
    --border: #e2e8f0;
  }

  .order-detail-wrapper {
    max-width: 1400px;
    margin: 0 auto;
  }

  /* ===== Header Section ===== */
  .order-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 20px;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    color: white;
    box-shadow: 0 10px 25px rgba(30, 64, 175, 0.15);
    position: relative;
    overflow: hidden;
  }
  .order-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
  }
  .order-header h1 {
    font-weight: 800;
    font-size: 1.875rem;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .order-header h1 i {
    font-size: 2rem;
  }
  .order-header p {
    opacity: 0.95;
    margin: 0;
    font-size: 1.063rem;
  }
  .btn-back {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    padding: 0.600rem 1.5rem;
    border-radius: 10px;
    font-weight: 300;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
  }
  .btn-back:hover {
    background: white;
    color: var(--primary);
    border-color: white;
    transform: translateX(-4px);
  }

  /* ===== Info Cards Grid ===== */
  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  .info-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid var(--border);
    transition: all 0.3s ease;
    position: relative;
  }
  .info-card:hover {
    box-shadow: 0 8px 20px rgba(30, 64, 175, 0.12);
    transform: translateY(-2px);
  }
  .info-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }
  .info-card-icon.primary {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: var(--primary);
  }
  .info-card-icon.success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: var(--success);
  }
  .info-card-icon.danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: var(--danger);
  }
  .info-card-label {
    font-size: 0.813rem;
    color: var(--gray);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
  }
  .info-card-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1e293b;
  }
  .info-card.highlight {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: var(--primary);
  }
  .info-card.highlight .info-card-value {
    color: var(--primary);
    font-size: 1.5rem;
  }

  /* ===== Customer Info Section ===== */
  .customer-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    border: 1px solid var(--border);
  }
  .section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .section-title i {
    font-size: 1.5rem;
    color: var(--primary);
  }
  .customer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
  }
  .customer-item {
    display: flex;
    align-items: start;
    gap: 1rem;
    padding: 1rem;
    background: var(--gray-light);
    border-radius: 12px;
    border: 1px solid var(--border);
  }
  .customer-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
    flex-shrink: 0;
  }
  .customer-content .label {
    font-size: 0.75rem;
    color: var(--gray);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 0.25rem;
  }
  .customer-content .value {
    font-size: 0.938rem;
    font-weight: 600;
    color: #1e293b;
  }
  .customer-item.full-width {
    grid-column: 1 / -1;
  }

  /* ===== Badge System ===== */
  .badge-custom {
    padding: 0.625rem 1.25rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.875rem;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  .badge-paid { background: var(--success); color: white; }
  .badge-pending { background: var(--gray); color: white; }
  .badge-failed { background: var(--danger); color: white; }
  .badge-processing { background: #3b82f6; color: white; }
  .badge-shipped { background: var(--primary); color: white; }
  .badge-delivered { background: var(--success); color: white; }
  .badge-cancelled { background: var(--danger); color: white; }
  .badge-refunded { background: var(--gray); color: white; }

  .order-code-display {
    font-family: 'Courier New', monospace;
    font-weight: 800;
    color: var(--primary);
    font-size: 1rem;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    padding: 0.625rem 1.25rem;
    border-radius: 12px;
    border: 2px solid var(--primary);
  }

  /* ===== Items Table ===== */
  .items-section {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid var(--border);
  }
  .items-header {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    color: white;
    padding: 1.5rem 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .items-header h3 {
    margin: 0;
    font-weight: 700;
    font-size: 1.25rem;
  }
  .items-header i {
    font-size: 1.5rem;
  }

  .items-table {
    width: 100%;
    margin: 0;
  }
  .items-table thead {
    background: var(--gray-light);
  }
  .items-table thead th {
    font-weight: 700;
    font-size: 0.813rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    padding: 1.25rem 1.5rem;
    border: none;
    border-bottom: 2px solid var(--border);
  }
  .items-table tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f5f9;
  }
  .items-table tbody tr:hover {
    background: var(--gray-light);
    transform: scale(1.005);
  }
  .items-table tbody td {
    padding: 1.5rem;
    vertical-align: middle;
  }

  .product-image {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 14px;
    border: 3px solid var(--border);
    transition: all 0.3s ease;
    cursor: pointer;
  }
  .product-image:hover {
    transform: scale(1.15) rotate(2deg);
    border-color: var(--primary);
    box-shadow: 0 8px 16px rgba(30, 64, 175, 0.25);
  }

  .product-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 1rem;
    line-height: 1.4;
  }
  .price-text {
    font-weight: 600;
    color: #475569;
    font-size: 1rem;
  }
  .quantity-badge {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: var(--primary);
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 800;
    font-size: 1rem;
    display: inline-block;
    min-width: 50px;
    text-align: center;
  }
  .discount-text {
    color: var(--danger);
    font-weight: 700;
    font-size: 0.938rem;
  }
  .subtotal-text {
    font-weight: 800;
    color: var(--primary);
    font-size: 1.125rem;
  }

  /* ===== Table Footer ===== */
  .items-table tfoot {
    background: var(--gray-light);
  }
  .items-table tfoot tr {
    border-top: 2px solid var(--border);
  }
  .items-table tfoot th {
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    color: #475569;
    font-size: 1rem;
  }
  .items-table tfoot tr:last-child {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  }
  .items-table tfoot tr:last-child th {
    font-size: 1.375rem;
    color: var(--primary);
    font-weight: 800;
    padding: 1.5rem;
  }

  /* ===== Empty State ===== */
  .empty-state {
    padding: 4rem 2rem;
    text-align: center;
  }
  .empty-state i {
    font-size: 5rem;
    color: #cbd5e0;
    margin-bottom: 1.5rem;
    opacity: 0.5;
  }
  .empty-state p {
    color: var(--gray);
    font-size: 1.25rem;
    margin: 0;
    font-weight: 500;
  }

  /* ===== Responsive ===== */
  @media (max-width: 768px) {
    .order-header {
      padding: 1.5rem;
    }
    .order-header h1 {
      font-size: 1.5rem;
    }
    .info-grid,
    .customer-grid {
      grid-template-columns: 1fr;
    }
    .product-image {
      width: 56px;
      height: 56px;
    }
    .items-table thead th,
    .items-table tbody td,
    .items-table tfoot th {
      padding: 1rem;
      font-size: 0.875rem;
    }
  }
</style>

<div class="container-fluid py-4">
  <div class="order-detail-wrapper">

    {{-- Header --}}
    <div class="order-header">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div style="position: relative; z-index: 1;">
          <h1>
            <i class="bi bi-receipt-cutoff"></i>
            Chi tiết đơn hàng
          </h1>
          
          <p>Theo dõi thông tin và trạng thái đơn hàng của bạn</p>
        </div>
        <br>
        <a href="{{ route('admin.orders.index') }}" class="btn-back">
          <i class="bi bi-arrow-left me-2"></i>Quay lại
        </a>
      </div>
    </div>

    {{-- Info Cards Grid --}}
    <div class="info-grid">
      <div class="info-card">
        <div class="info-card-icon primary">
          <i class="bi bi-hash"></i>
        </div>
        <div class="info-card-label">Mã đơn hàng</div>
        <div class="info-card-value">
          <span class="order-code-display">#{{ $order->order_code ?? $order->code ?? $order->id }}</span>
        </div>
      </div>

      <div class="info-card">
        <div class="info-card-icon {{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'primary') }}">
          <i class="bi {{ $payIcon }}"></i>
        </div>
        <div class="info-card-label">Thanh toán</div>
        <div class="info-card-value">
          <span class="badge-custom {{ $payClass }}">
            <i class="bi {{ $payIcon }}"></i>
            {{ $payMap[$order->payment_status] ?? ucfirst($order->payment_status ?? 'pending') }}
          </span>
        </div>
      </div>

      <div class="info-card">
        <div class="info-card-icon {{ in_array($order->status, ['delivered']) ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}">
          <i class="bi {{ $statusIcon }}"></i>
        </div>
        <div class="info-card-label">Trạng thái đơn</div>
        <div class="info-card-value">
          <span class="badge-custom {{ $statusClass }}">
            <i class="bi {{ $statusIcon }}"></i>
            {{ $statusMap[$order->status] ?? ucfirst($order->status ?? 'pending') }}
          </span>
        </div>
      </div>

      <div class="info-card">
        <div class="info-card-icon primary">
          <i class="bi bi-clock-history"></i>
        </div>
        <div class="info-card-label">Ngày tạo</div>
        <div class="info-card-value">{{ optional($order->created_at)->format('d/m/Y H:i') ?? '—' }}</div>
      </div>


    </div>

    {{-- Customer Information --}}
    <div class="customer-section">
      <h3 class="section-title">
        <i class="bi bi-person-circle"></i>
        Thông tin khách hàng
      </h3>
      <div class="customer-grid">
        <div class="customer-item">
          <div class="customer-icon">
            <i class="bi bi-person-fill"></i>
          </div>
          <div class="customer-content">
            <div class="label">Họ tên</div>
            <div class="value">{{ $name }}</div>
          </div>
        </div>

        @if($phone)
        <div class="customer-item">
          <div class="customer-icon">
            <i class="bi bi-telephone-fill"></i>
          </div>
          <div class="customer-content">
            <div class="label">Số điện thoại</div>
            <div class="value">{{ $phone }}</div>
          </div>
        </div>
        @endif

        @if($email)
        <div class="customer-item">
          <div class="customer-icon">
            <i class="bi bi-envelope-fill"></i>
          </div>
          <div class="customer-content">
            <div class="label">Email</div>
            <div class="value">{{ $email }}</div>
          </div>
        </div>
        @endif

        @if($addr)
        <div class="customer-item full-width">
          <div class="customer-icon">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
          <div class="customer-content">
            <div class="label">Địa chỉ giao hàng</div>
            <div class="value">{{ $addr }}</div>
          </div>
        </div>
        @endif
      </div>
    </div>

    {{-- Items Table --}}
    <div class="items-section">
      <div class="items-header">
        <i class="bi bi-box-seam"></i>
        <h3>Sản phẩm/Dịch vụ</h3>
      </div>
      <div class="table-responsive">
        <table class="items-table">
          <thead>
            <tr>
              <th style="width:100px;">Ảnh</th>
              <th>Tên sản phẩm</th>
              <th class="text-end">Đơn giá</th>
              <th class="text-center" style="width:120px;">Số lượng</th>
              <th class="text-end">Giảm giá</th>
              <th class="text-end">Thành tiền</th>
            </tr>
          </thead>

          <tbody>
          @forelse($order->items as $it)
            @php
              $prod = $it->product;
              $svc  = $it->service;

              $nameItem = $prod->product_name
                        ?? $prod->name
                        ?? $svc->name
                        ?? $svc->service_name
                        ?? ('Mục #'.($it->product_id ?? $it->service_id ?? $it->id));

              $raw = optional($prod?->mainImageRel)->url
                  ?? optional($prod?->imagesRel->first())->url
                  ?? $prod->thumbnail
                  ?? $svc->thumbnail
                  ?? $svc->image
                  ?? null;
              $thumb = $toUrl($raw) ?? asset('images/placeholder-4x3.png');

              $giam = !is_null($it->discount_price)
                        ? $money($it->discount_price)
                        : ((float)$it->discount_percent > 0
                            ? rtrim(rtrim(number_format($it->discount_percent,2,'.',''), '0'),'.') . '%'
                            : '—');
            @endphp

            <tr>
              <td><img src="{{ $thumb }}" alt="{{ $nameItem }}" class="product-image"></td>
              <td><div class="product-name">{{ $nameItem }}</div></td>
              <td class="text-end"><span class="price-text">{{ $money($it->price) }}</span></td>
              <td class="text-center"><span class="quantity-badge">{{ $it->quantity }}</span></td>
              <td class="text-end"><span class="discount-text">{{ $giam }}</span></td>
              <td class="text-end"><span class="subtotal-text">{{ $money($it->subtotal) }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="6">
                <div class="empty-state">
                  <i class="bi bi-inbox"></i>
                  <p>Chưa có sản phẩm trong đơn hàng</p>
                </div>
              </td>
            </tr>
          @endforelse
          </tbody>

          @if($order->items->isNotEmpty())
          <tfoot>
            <tr>
              <th colspan="5" class="text-end">Tạm tính</th>
              <th class="text-end">{{ $money($itemsTotal) }}</th>
            </tr>
            <tr>
              <th colspan="5" class="text-end"><i class="bi bi-receipt me-2"></i>VAT (5%)</th>
              <th class="text-end">{{ $money($vatAmount) }}</th>
            </tr>
            <tr>
              <th colspan="5" class="text-end"><i class="bi bi-calculator me-2"></i>Tổng cộng</th>
              <th class="text-end">{{ $money($grandTotal) }}</th>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>

  </div>
</div>
@endsection
@extends('dashboard.layouts.app')

@section('breadcrumb-parent','Quản trị')
@section('breadcrumb-child','Đơn hàng')
@section('page-title','Quản lý Đơn hàng')

@section('content')
<style>
  /* Page header giống trang sản phẩm */
  .page-header{
    background:linear-gradient(135deg,#1e40af 0%,#3b82f6 100%);
    border-radius:16px;padding:2rem;margin-bottom:2rem;color:#fff;position:relative;overflow:hidden;
  }
  .btn-excel{
    background:linear-gradient(135deg,#2563eb 0%,#3b82f6 100%);
    border:none;color:#fff;padding:12px 20px;border-radius:12px;font-weight:600;
    box-shadow:0 6px 18px rgba(59,130,246,.35);display:inline-flex;align-items:center;gap:.5rem;
  }
  .btn-excel:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(59,130,246,.45)}
  .btn-add{
    background:linear-gradient(135deg,#22c55e 0%,#16a34a 100%);border:none;color:#fff;padding:12px 20px;border-radius:12px;font-weight:600;
    box-shadow:0 6px 18px rgba(37,99,235,.35);
  }
  /* Bảng giống table-modern của trang sản phẩm */
  /* Giảm width tổng thể */
.table-modern {
    font-size: 0.9rem;
    width: 100%;
    table-layout: fixed; /* RẤT QUAN TRỌNG: chia đều các cột */
}

/* Giảm khoảng cách trong các ô */
.table-modern th, 
.table-modern td {
    padding: 0.6rem !important;
    white-space: nowrap; /* không xuống dòng lung tung */
    overflow: hidden;
    text-overflow: ellipsis; /* hiện dấu ... khi dài */
}

/* Thu nhỏ riêng từng cột để bảng gọn hơn */
.table-modern th:nth-child(1),
.table-modern td:nth-child(1) { width: 90px; }

.table-modern th:nth-child(2),
.table-modern td:nth-child(2) { width: 180px; }

.table-modern th:nth-child(3),
.table-modern td:nth-child(3) { width: 160px; }

.table-modern th:nth-child(4),
.table-modern td:nth-child(4) { width: 110px; }

.table-modern th:nth-child(5),
.table-modern td:nth-child(5) { width: 130px; }

.table-modern th:nth-child(6),
.table-modern td:nth-child(6) { width: 130px; }

.table-modern th:nth-child(7),
.table-modern td:nth-child(7) { width: 140px; }

.table-modern th:nth-child(8),
.table-modern td:nth-child(8) { width: 110px; }

/* Trung tâm toàn bộ bảng */
.table-modern td, .table-modern th {
    text-align: center !important;
    vertical-align: middle !important;
}

  .status-badge{padding:8px 16px;border-radius:25px;font-size:.875rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
  .badge-paid{background:#10b981;color:#fff}.badge-pending{color:cornflowerblue}.badge-failed{background:#ef4444;color:#fff}
  .badge-processing{background:#3b82f6;color:#fff}.badge-shipped{background:#1e40af;color:#fff}
  .badge-delivered{background:#10b981;color:#fff}.badge-cancelled{background:#ef4444;color:#fff}.badge-refunded{background:#64748b;color:#fff}
  .items-count{background:#dbeafe;color:#1e40af;padding:.5rem .875rem;border-radius:10px;font-weight:700;display:inline-block}
  .total-amount{font-weight:700;color:#1e40af}
</style>

@php
  // Fallback map nếu controller không truyền vào
  $statusMap = $statusMap ?? [
    'pending' => 'Chờ xử lý',
    'processing' => 'Đang xử lý',
    'shipped' => 'Đã gửi',
    'delivered' => 'Đã giao',
    'cancelled' => 'Hủy',
    'refunded' => 'Hoàn tiền',
  ];
  $payMap = $payMap ?? [
    'pending' => 'Chờ thanh toán',
    'paid'    => 'Đã thanh toán',
    'failed'  => 'Thất bại',
  ];
@endphp

<div class="container-fluid">

  {{-- HEADER (giống trang sản phẩm) --}}
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="mb-2" style="font-size:2.5rem;font-weight:700;">
          <i class="fas fa-receipt me-2"></i> Quản lý Đơn hàng
        </h1>
        <p class="mb-0" style="font-size:1.1rem;opacity:.9;">
          Theo dõi & xử lý đơn hàng trong hệ thống
        </p>
      </div>
      <div class="col-md-4">
        <div class="d-flex justify-content-md-end gap-2">
          <a href="" class="btn-excel">
            <i class="fas fa-download"></i> Xuất Excel
          </a>
          {{-- (Tuỳ nhu cầu) --}}
          {{-- <a href="{{ route('admin.orders.create') }}" class="btn-add"><i class="fas fa-plus me-1"></i> Tạo đơn</a> --}}
        </div>
      </div>
    </div>
  </div>

  {{-- FILTER (đưa về style form giống trang sản phẩm) --}}
  <form method="get" class="d-flex flex-wrap gap-3 align-items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Mã đơn, tên KH, sđt, email"
           class="px-3 py-2 border border-gray-200 rounded-lg w-64 focus:ring-2 focus:ring-blue-500 focus:border-transparent">

    <select name="status"
            class="px-5 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      <option value="">Tất cả trạng thái</option>
      @foreach($statusMap as $k=>$v)
        <option value="{{ $k }}" @selected(request('status')===(string)$k)>{{ $v }}</option>
      @endforeach
    </select>

    <select name="payment_status"
            class="px-5 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      <option value="">Tất cả thanh toán</option>
      @foreach($payMap as $k=>$v)
        <option value="{{ $k }}" @selected(request('payment_status')===(string)$k)>{{ $v }}</option>
      @endforeach
    </select>

    <input type="date" name="date_from" value="{{ request('date_from') }}"
           class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    <input type="date" name="date_to" value="{{ request('date_to') }}"
           class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">

    <button class="px-5 py-2 bg-blue-50 text-blue-600 font-medium rounded-lg hover:bg-blue-100">
      <i class="fas fa-filter me-2"></i>Lọc
    </button>

    <a href="{{ route('admin.orders.index') }}"
       class="px-5 py-2 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100">
      Xoá lọc
    </a>
  </form>

  {{-- TABLE (style giống table sản phẩm) --}}
  <div class="table-responsive">
    <table class="table table-modern">
      <thead>
        <tr>
          <th>Mã đơn hàng</th>
          <th>Khách hàng</th>
          <th>Liên hệ</th>
          <th>Sản phẩm</th>
          <th>Tổng tiền</th>
          <th>Thanh toán</th>
          <th>Trạng thái</th>
         
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
      @forelse($orders as $o)
        @php
          $payClass = match($o->payment_status){
            'paid' => 'badge-paid','failed' => 'badge-failed', default => 'badge-pending'
          };
          $statusClass = match($o->status){
            'processing'=>'badge-processing','shipped'=>'badge-shipped','delivered'=>'badge-delivered',
            'cancelled'=>'badge-cancelled','refunded'=>'badge-refunded', default=>'badge-pending'
          };
          $itemsCount = $o->items_count ?? ($o->items->count() ?? 0);
          $total = $o->total_with_vat ?? $o->grand_total ?? $o->total ?? 0;
          $phone = $o->phone ?: ($o->user?->phone ?? null);
          $email = $o->user?->email ?? null;
          $address = $o->shipping_address ?: ($o->user?->customer?->address ?? null);
        @endphp
        <tr>
          <td><span class="fw-bold" style="color:#1e40af">#{{ $o->order_code ?? $o->order_id }}</span></td>

          <td class="text-start">
            <div class="fw-semibold">{{ $o->user?->name ?? '—' }}</div>
            @if($address)
              <div class="text-muted small"><i class="fas fa-location-dot me-1"></i>{{ $address }}</div>
            @endif
          </td>

          <td class="text-start">
            @if($phone)
              <div class="text-muted small"><i class="fas fa-phone me-1"></i>{{ $phone }}</div>
            @endif
            @if($email)
              <div class="text-muted small"><i class="fas fa-envelope me-1"></i>{{ $email }}</div>
            @endif
            @if(!$phone && !$email)
              <span class="text-muted">—</span>
            @endif
          </td>

          <td><span class="items-count">{{ $itemsCount }}</span></td>

          <td><div class="total-amount">{{ number_format($total,0,',','.') }}₫</div></td>

          <td><span style="align-content: center" class="status-badge {{ $payClass }}">{{ $payMap[$o->payment_status] ?? ucfirst($o->payment_status) }}</span></td>

          <td><span style="align-content: center"  class="status-badge {{ $statusClass }}">{{ $statusMap[$o->status] ?? ucfirst($o->status) }}</span></td>

       

          <td>
            <div class="d-flex justify-content-center gap-2">
              <a href="{{ route('admin.orders.show',$o) }}" class="btn btn-sm"
                 style="background:#1e40af;color:#fff;border-radius:10px;font-weight:600;">
                <i class="fas fa-eye me-1"></i>Xem
              </a>
             <a href="javascript:void(0)" 
   class="btn btn-sm btn-warning text-white fw-bold"
   style="border-radius:12px; padding: 6px 14px; box-shadow: 0 2px 8px rgba(255,193,7,.3);"
   onclick="openEditModal(
     {{ $o->id ?? $o->order_id }},
     '{{ $o->status }}',
     '{{ $o->payment_status }}',
     `{!! addslashes(nl2br(e($o->shipping_address ?? ''))) !!}`,
     '{{ $o->order_code ?? $o->order_id }}'
   )">
   <i class="fas fa-edit me-1"></i> Sửa
</a>

            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="9" class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3"></i><br>Không có đơn hàng nào
          </td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- PAGINATION giống trang sản phẩm --}}
  @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="bg-white border-top px-4 py-3 d-flex justify-content-between align-items-center">
      <div class="text-sm text-gray-700">
        Hiển thị <span class="fw-semibold">{{ $orders->firstItem() }}</span>–<span class="fw-semibold">{{ $orders->lastItem() }}</span>
        trong tổng số <span class="fw-semibold">{{ $orders->total() }}</span> kết quả
      </div>
      <div>
        {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
      </div>
    </div>
  @endif

</div>



{{-- Font Awesome (nếu chưa có) --}}
<script>
if (!document.querySelector('link[href*="font-awesome"]')) {
  const l=document.createElement('link');l.rel='stylesheet';
  l.href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css';
  document.head.appendChild(l);
}
</script>
<script>
function loadOrderData(id) {
    fetch(`/admin/orders/${id}/json`)
        .then(res => res.json())
        .then(o => {
            document.getElementById('edit_status').value = o.status ?? '';
            document.getElementById('edit_payment_status').value = o.payment_status ?? '';
            document.getElementById('edit_address').value = o.shipping_address ?? '';

            // Set đúng action update
            document.getElementById('editOrderForm').action = `/admin/orders/${id}`;
        })
        .catch(err => console.error('Lỗi load order:', err));
}
</script>

<!-- EDIT ORDER MODAL - ĐẢM BẢO HIỆN ĐÚNG GIỮA MÀN HÌNH -->
<div class="modal fade" id="editOrderModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 shadow-xl border-0" style="max-height: 95vh;">

      <div class="modal-header border-0 text-white position-relative" 
           style="background: linear-gradient(135deg, #1e40af, #3b82f6); border-radius: 16px 16px 0 0;">
        <h5 class="modal-title fw-bold fs-5 d-flex align-items-center gap-2">
          <i class="fas fa-edit"></i>
          Chỉnh sửa đơn hàng <span id="modalOrderCode" class="text-warning">#000000</span>
        </h5>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editOrderForm" method="POST" action="">
        @csrf
        @method('PUT')

        <div class="modal-body p-4 pt-3">
          <div class="alert alert-primary border-0 rounded-3 py-3 text-center mb-4">
            <i class="fas fa-receipt me-2"></i>
            <strong>Đơn hàng:</strong> <span id="displayOrderCode" class="fs-5">#000000</span>
          </div>

          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label fw-bold text-dark">
                <i class="fas fa-truck me-1"></i> Trạng thái đơn hàng
              </label>
              <select name="status" id="edit_status" class="form-select form-select-lg rounded-pill shadow-sm" required>
                @foreach($statusMap as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold text-dark">
                <i class="fas fa-credit-card me-1"></i> Trạng thái thanh toán
              </label>
              <select name="payment_status" id="edit_payment_status" class="form-select form-select-lg rounded-pill shadow-sm" required>
                @foreach($payMap as $k => $v)
                  <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="mt-3">
            <label class="form-label fw-bold text-dark">
              <i class="fas fa-map-location-dot me-1"></i> Địa chỉ giao hàng
            </label>
            <textarea name="shipping_address" id="edit_address" rows="3" 
                      class="form-control rounded-3 shadow-sm" 
                      placeholder="Nhập địa chỉ giao hàng đầy đủ..."></textarea>
          </div>
        </div>

        <div class="modal-footer border-0 bg-light rounded-bottom-4 py-3">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Hủy
          </button>
          <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
            <i class="fas fa-save"></i> Lưu thay đổi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- SCRIPT MỞ MODAL - ĐẢM BẢO KHÔNG BỊ ĐẨY XUỐNG DƯỚI -->
<script>
// Đảm bảo modal luôn hiện giữa màn hình dù scroll ở đâu
document.getElementById('editOrderModal')?.addEventListener('show.bs.modal', function () {
  // Fix trường hợp body bị thêm padding khi có scrollbar
  document.body.style.paddingRight = '0px';
  document.body.classList.remove('modal-open'); // tạm bỏ để tránh lỗi scroll
  setTimeout(() => {
    document.body.classList.add('modal-open');
  }, 100);
});

function openEditModal(orderId, status, paymentStatus, address, orderCode) {
  const code = orderCode || orderId;

  // Điền dữ liệu
  document.getElementById('modalOrderCode').textContent = code;
  document.getElementById('displayOrderCode').textContent = '#' + code;
  document.getElementById('edit_status').value = status || 'pending';
  document.getElementById('edit_payment_status').value = paymentStatus || 'pending';
  document.getElementById('edit_address').value = address ? address.replace(/\\n/g, '\n') : '';

  // Set URL update
  document.getElementById('editOrderForm').action = `/admin/orders/${orderId}`;

  // Mở modal (dùng Bootstrap 5 API chính thức)
  const modal = new bootstrap.Modal(document.getElementById('editOrderModal'), {
    backdrop: 'static',
    keyboard: false
  });
  modal.show();
}
</script>
@endsection

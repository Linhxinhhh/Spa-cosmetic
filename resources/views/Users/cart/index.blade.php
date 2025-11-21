@extends('Users.layouts.home')

@section('title','Giỏ hàng')

@push('styles')
<style>
  :root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --success-color: #059669;
    --danger-color: #dc2626;
    --warning-color: #d97706;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --border-radius: 12px;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  }

  body {
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .breadcrumb .breadcrumb-item + .breadcrumb-item::before{
  content: '›';
  color: #adb5bd;          /* xám nhạt */
  padding: 0 .5rem;
}
  /* Làm dấu phân cách TO và ĐẬM */
.breadcrumb-chevron .breadcrumb-item + .breadcrumb-item::before{
  font-size: 22px;        /* <— tăng/giảm tùy ý (18–26px) */
  font-weight: 500;
  line-height: 1;
  color: #6c757d;         /* màu xám; đổi sang #f97316 nếu muốn cam */
  position: relative;
  top: -1px;              /* chỉnh viền dọc cho cân */
  padding-right: .75rem;  /* nới khoảng cách */
}

/* Nới khoảng bên trái item sau */
.breadcrumb-chevron .breadcrumb-item + .breadcrumb-item{}
      .cat-link
  {
    color: #3f3f46;
  }
  .cat-link:hover
  {
    color: gray; 
  }
  .page-title {
    color: var(--gray-900);
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 2rem;
    position: relative;
  }
  .page-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    border-radius: 2px;
  }

  .cart-card { background: white; border: 1px solid var(--gray-200); border-radius: var(--border-radius); box-shadow: var(--shadow-md); overflow: hidden; transition: all 0.3s ease; }
  .cart-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }

  .cart-thead {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
    border-bottom: 2px solid var(--gray-200);
    color: var(--gray-700);
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .payment-option {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: 0.3s;
    background: #fff;

    /* Căn giữa nội dung */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 120px; /* Chiều cao card */
}

.payment-option img {
    max-width: 80%;
    max-height: 70px;
    object-fit: contain;
}

.payment-option.active {
    border-color: #2563eb;
    background: #f0f7ff;
    box-shadow: 0 0 10px rgba(37, 99, 235, 0.3);
}


.payment-option input {
    display: none;
}

  .cart-item { border-bottom: 1px solid var(--gray-100); transition: all 0.3s ease; position: relative; }
  .cart-item:hover { background: var(--gray-50); }
  .cart-item:last-child { border-bottom: none; }

  .product-thumb { width: 96px; height: 96px; border-radius: var(--border-radius); overflow: hidden; background: white; border: 2px solid var(--gray-100); transition: all 0.3s ease; position: relative; }
  .product-thumb:hover { transform: scale(1.05); border-color: var(--primary-color); box-shadow: var(--shadow-md); }
  .product-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
  .product-thumb:hover img { transform: scale(1.1); }

  .product-name { font-weight: 600; color: var(--gray-900); text-decoration: none; transition: color 0.3s ease; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .product-name:hover { color: var(--primary-color); }

  .gift-badge { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: var(--success-color); border-radius: 20px; padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 500; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 0.25rem; }
  .gift-badge::before { content: '🎁'; font-size: 0.875rem; }

  .mini-thumb { width: 48px; height: 48px; border-radius: 8px; overflow: hidden; border: 1px solid var(--gray-200); margin-right: 0.75rem; }
  .mini-thumb img { width: 100%; height: 100%; object-fit: cover; }

  .price-current { font-weight: 700; color: var(--danger-color); font-size: 1.125rem; }
  .price-original { color: var(--gray-400); text-decoration: line-through; font-size: 0.875rem; }

  .qty-input { width: 80px; text-align: center; border: 2px solid var(--gray-200); border-radius: 8px; padding: 0.5rem; font-weight: 600; transition: all 0.3s ease; }
  .qty-input:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

  .remove-btn { background: none; border: none; color: var(--gray-400); padding: 0.5rem; border-radius: 8px; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; }
  .remove-btn:hover { color: var(--danger-color); background: #fef2f2; }

  .summary-card { background: white; border: 1px solid var(--gray-200); border-radius: var(--border-radius); box-shadow: var(--shadow-md); position: sticky; top: 2rem; }
  .summary-title { color: var(--gray-900); font-weight: 700; font-size: 1.5rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--gray-100); }

  .summary-row { display: flex; justify-content: between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); }
  .summary-row:last-child { border-bottom: none; }
  .summary-total { background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%); margin: 1.5rem -1.5rem -1.5rem; padding: 1.5rem; border-top: 2px solid var(--gray-200); }
  .total-amount { font-size: 1.75rem; font-weight: 800; color: var(--danger-color); }

  .btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    border: none; border-radius: 12px; padding: 1rem 2rem; font-weight: 600; font-size: 1rem; color: white; transition: all 0.3s ease; position: relative; overflow: hidden;
  }
  .btn-primary::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent); transition: left 0.5s ease; }
  .btn-primary:hover::before { left: 100%; }
  .btn-primary:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }

  .continue-shopping { display: inline-flex; align-items: center; gap: 0.5rem; color: var(--gray-600); text-decoration: none; font-weight: 500; padding: 0.75rem 1rem; border-radius: 8px; transition: all 0.3s ease; }
  .continue-shopping:hover { color: var(--primary-color); background: var(--gray-50); transform: translateX(-4px); }

  .empty-cart { text-align: center; padding: 4rem 2rem; color: var(--gray-500); }
  .empty-cart-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }

  @media (max-width: 768px) {
    .container { margin: 1rem; padding: 1rem; border-radius: 16px; }
    .cart-thead { display: none !important; }
    .cart-item { display: block !important; }
    .product-info { margin-bottom: 1rem; }
    .item-actions { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
    .summary-card { position: static; margin-top: 2rem; }
  }

  @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  .cart-item { animation: fadeInUp 0.5s ease-out; }
  .qty-input:disabled { opacity: 0.6; cursor: not-allowed; }
  .updated { background: #f0fdf4 !important; border-left: 4px solid var(--success-color); }
</style>
@endpush

@section('content')
<div class="container">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.home') }}"  class="cat-link">Trang chủ</a></li>
      <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
    </ol>
  </nav>

  <h1 class="page-title">
    Giỏ hàng của bạn
    <small class="text-muted fs-5 fw-normal">({{ $items->count() }} sản phẩm)</small>
  </h1>

  <div class="row g-4">
    {{-- LEFT: Cart Items --}}
    <div class="col-lg-8">
      <div class="cart-card">
        {{-- Desktop Header --}}
        <div class="cart-thead px-4 py-3 d-none d-lg-grid"
             style="grid-template-columns: 1fr 120px 120px 140px;">
          <div>Sản phẩm</div>
          <div class="text-center">Đơn giá</div>
          <div class="text-center">Số lượng</div>
          <div class="text-end">Thành tiền</div>
        </div>

        <div class="p-4">
          @php $isAuth = auth()->check(); @endphp

          @forelse ($items as $index => $it)
            @php
              // $it: CartItem (user) hoặc object { product, quantity } (guest)
              $p         = $it->product;
              $rowId     = $isAuth ? ($it->id ?? ($it->getKey() ?? null)) : $p->product_id; // id cho route
              $qty       = (int) $it->quantity;
              $price     = product_final_price($p);
              $orig      = (float)($p->price ?? 0);
              $hasSale   = $price > 0 && $price < $orig;
              $lineTotal = $price * $qty;
            @endphp

            <div class="cart-item d-lg-grid align-items-center py-4"
                 style="grid-template-columns: 1fr 120px 120px 140px; animation-delay: {{ $index * 0.1 }}s;">

              {{-- Product Info --}}
              <div class="product-info d-flex align-items-start gap-3">
                <a class="product-thumb" href="{{ route('users.products.show', $p->slug) }}">
                  <img src="{{ product_main_src($p) }}" alt="{{ $p->product_name }}" loading="lazy">
                </a>

                <div class="flex-grow-1">
                  <a class="product-name d-block mb-2"
                     href="{{ route('users.products.show', $p->slug) }}">
                    {{ $p->product_name }}
                  </a>

                  {{-- Gift Badge --}}
                  <div class="mb-3">
                    <span class="gift-badge">
                      Tặng kèm mặt nạ làm dịu da 25ml
                    </span>
                  </div>

                  {{-- Gift Detail --}}
                  <div class="d-flex align-items-center mb-3">
                    <div class="mini-thumb">
                      <img src="{{ product_hover_src($p) ?? product_main_src($p) }}"
                           alt="Gift item" loading="lazy">
                    </div>
                    <div class="small">
                      <div class="fw-semibold">Tặng kèm: {{ $p->product_name }} Mini 30ml</div>
                      <div class="text-success">
                        Trị giá: <span class="fw-semibold">{{ number_format(max(0, round($price*0.6)),0,',','.') }}₫</span>
                      </div>
                    </div>
                  </div>

                  {{-- Remove Button (đúng route + không dùng $id chưa khai báo) --}}
                  <form method="POST"
                        action="{{ route('users.cart.remove', $rowId) }}"
                        onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')"
                        class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="remove-btn">
                      <i class="fas fa-trash-alt"></i>
                      Xóa khỏi giỏ
                    </button>
                  </form>
                </div>
              </div>

              {{-- Price --}}
              <div class="text-lg-center mb-3 mb-lg-0">
                <div class="price-current">{{ number_format($price,0,',','.') }}₫</div>
                @if($hasSale)
                  <div class="price-original">{{ number_format($orig,0,',','.') }}₫</div>
                @endif
                <div class="d-lg-none small text-muted">Đơn giá</div>
              </div>

              {{-- Quantity --}}
              <div class="text-lg-center mb-3 mb-lg-0">
                <form method="POST" action="{{ route('users.cart.update', $rowId) }}">
                  @csrf @method('PATCH')
                  <input type="number"
                         name="quantity"
                         class="qty-input form-control"
                         min="1"
                         max="99"
                         value="{{ $qty }}"
                         onchange="this.form.submit()">
                </form>
                <div class="d-lg-none small text-muted mt-1">Số lượng</div>
              </div>

              {{-- Line Total --}}
              <div class="text-lg-end">
                <div class="price-current fs-5">{{ number_format($lineTotal,0,',','.') }}₫</div>
                <div class="d-lg-none small text-muted">Thành tiền</div>
              </div>
            </div>
          @empty
            <div class="empty-cart">
              <div class="empty-cart-icon">🛒</div>
              <h3 class="mb-3">Giỏ hàng trống</h3>
              <p class="mb-4">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm!</p>
              <a href="{{ route('users.products.index') }}" class="btn btn-primary">
                Khám phá sản phẩm
              </a>
            </div>
          @endforelse
        </div>
      </div>

      {{-- Continue Shopping --}}
      @if($items->count())
        <div class="mt-4">
          <a href="{{ route('users.products.index') }}" class="continue-shopping">
            <i class="fas fa-arrow-left"></i>
            Tiếp tục mua sắm
          </a>
        </div>
      @endif
    </div>
<div class="col-lg-4">
    <div class="summary-card p-4">

        <h3 class="summary-title">Tóm tắt đơn hàng</h3>

        {{-- Giá tạm tính --}}
        <div class="summary-row">
            <span>Tạm tính</span>
            <span class="fw-bold">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
        </div>

        {{-- Tổng tiền --}}
        <div class="summary-row summary-total">
            <span class="fw-bold">Tổng cộng</span>
            <span class="total-amount">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
        </div>

        {{-- Form thanh toán --}}
        <form id="checkoutForm" action="{{ route('users.checkout.pay') }}" method="POST">
            @csrf

            <h4 class="mt-4 mb-3 fw-bold text-black summary-title">Thông tin nhận hàng</h4>

            <input type="hidden" name="order_code" value="{{ $orderCode }}">

            <div class="mb-3">
                <label class="form-label fw-semibold">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Địa chỉ</label>
                <input type="text" name="address" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ghi chú</label>
                <textarea name="note" class="form-control"></textarea>
            </div>

            {{-- PHƯƠNG THỨC THANH TOÁN --}}
            <label class="form-label fw-semibold">Phương thức thanh toán</label>

            <div class="row g-3">

                {{-- VNPay --}}
                <div class="col-md-6">
                    

                    <label class="payment-option" for="payment-vnpay">
                        <div class="payment-card">
                            <div class="payment-icon">
                                <img src="/pay/vnpay.png" alt="VNPay">
                            </div>
                        </div>
                    </label>
                </div>

                {{-- MoMo --}}
                <div class="col-md-6">
                    
                    <label class="payment-option" for="payment-momo">
                        <div class="payment-card">
                            <div class="payment-icon">
                                <img src="/pay/momo.png" alt="Momo">
                            </div>
                        </div>
                    </label>
                </div>

            </div>

            <button type="submit" class="btn btn-primary w-100 btn-lg mt-4">
                <i class="fas fa-credit-card me-2"></i>
                Tiến hành thanh toán
            </button>
        </form>

    </div>
</div>




  </div>
</div>
@endsection

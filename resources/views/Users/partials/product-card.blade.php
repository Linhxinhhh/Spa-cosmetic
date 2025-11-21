@props([
  'product',
  'col' => 'col-6 col-md-4 col-lg-3',
  'showAddToCart' => true,
  'showRating' => true,
  'showStatusBadge' => true,
  'hoverSwap' => true,
])

@php
  // Lấy danh sách ảnh từ helper của bạn (đã có sẵn)
  $candidates = product_image_candidates($product);   // [0] main, [1] phụ nếu có
  $mainSrc    = product_main_src($product);
  $hoverSrc   = $candidates[1] ?? null;
  $hasHover   = $hoverSwap && !empty($hoverSrc);      // chỉ bật khi thật sự có ảnh 2

  $final   = product_final_price($product);
  $orig    = (float)($product->price ?? 0);
  $hasSale = $final > 0 && $final < $orig;

  $isOut   = (string)($product->status ?? '') === '3';
  $isNew   = optional($product->created_at)->gt(now()->subDays(30));

  $detailUrl = route('users.products.show', $product->slug ?? $product->product_id);
@endphp

<div class="{{ $col }}">
  <div class="product-card rounded wow fadeInUp" data-wow-delay="0.1s">
    <div class="product-card-inner border rounded position-relative">
      <div class="product-figure position-relative">
        <div class="product-thumb">
          <img src="{{ product_main_src($product) }}" class="main-img">
          @if(product_hover_src($product))
            <img src="{{ product_hover_src($product) }}" class="hover-img">
          @endif
        </div>

          @php
            $badge = null; $badgeClass = ''; $badgeText = '';

            if ($showStatusBadge) {
                if ($isOut) {
                    $badge = 'out';
                    $badgeClass = 'bg-secondary';
                    $badgeText  = 'Hết hàng';
                } elseif ($hasSale) {
                    $badge = 'sale';
                    $badgeClass = 'bg-danger';
                    $badgeText  = 'Sale';
                } elseif ($isNew) {
                    $badge = 'new';
                    // dùng màu Bootstrap 5 “subtle” nếu có, fallback sang success
                    $badgeClass = 'bg-success';
                    $badgeText  = 'New';
                }
            }
          @endphp

          @if($badge)
            <span class="badge {{ $badgeClass }} position-absolute top-0 end-0 m-2">
              {{ $badgeText }}
            </span>
          @endif


        <div class="product-quick">
          <a href="{{ $detailUrl }}" style="align-items: center" class="btn btn-light btn-sm rounded-pill shadow-sm">
            <i class="fa fa-eye me-1"></i> Xem nhanh
          </a>
        </div>
      </div>

      <div class="text-center rounded-bottom p-3">
        @if(!empty($product->category))
          <a href="{{ route('users.products.byCategory', $product->category->slug ?? $product->category_id) }}"
             class="small text-muted d-block mb-1">
            {{ $product->category->category_name }}
          </a>
        @endif

        <a href="{{ $detailUrl }}" class="d-block fw-semibold text-truncate">
          {{ $product->product_name }}
        </a>

        <div class="mt-1">
          @if($hasSale)
            <del class="me-2 text-muted small">{{ number_format($orig,0,',','.') }}₫</del>
            <span class="text-primary fw-bold">{{ number_format($final,0,',','.') }}₫</span>
          @else
            <span class="text-dark fw-bold">{{ number_format($final,0,',','.') }}₫</span>
          @endif
        </div>

        @if($showRating)
          <div class="d-flex justify-content-center gap-1 mt-2 text-warning">
            <i class="fas fa-star"></i><i class="fas fa-star"></i>
            <i class="fas fa-star"></i><i class="fas fa-star"></i>
            <i class="far fa-star"></i>
          </div>
        @endif
      </div>
    </div>

    @if($showAddToCart)
      <div class="product-cta border border-top-0 rounded-bottom text-center p-3 pt-0">
   <form action="{{ route('users.cart.add', ['product' => $product->slug]) }}" method="POST" class="mb-4">
      @csrf
      <input type="hidden" name="quantity" value="1">
      <button type="submit" class="btn btn-primary border-secondary rounded-pill  py-2 px-9 d-inline-flex align-items-center gap-2 d-block mx-auto">
        <i class="fas fa-shopping-cart"></i>
        <span>Thêm vào giỏ</span>
      </button>
    </form>
      </div>
    @endif
  </div>
</div>
@once
@push('styles')
<style>
  .product-thumb{
    position:relative;height:200px;overflow:hidden;border-radius:12px;background:#fff;
    display:flex;align-items:center;justify-content:center ;margin-top: 15px;
  }
  .product-thumb img{
    position:absolute;inset:0;margin:auto;max-width:100%;max-height:100%;
    object-fit:contain;object-position:center;transition:opacity .25s ease;will-change:opacity;
  }

  /* trạng thái mặc định */
  .product-thumb .main-img{opacity:1; z-index:1; visibility:visible !important;}
  .product-thumb .hover-img{opacity:0; z-index:2; visibility:hidden !important;}

  /* khi hover -> hiện ảnh phụ, ẩn ảnh chính */
  .product-thumb:hover .main-img{opacity:0; visibility:hidden !important;}
  .product-thumb:hover .hover-img{opacity:1; visibility:visible !important;}
  .product-quick {
  position: absolute;
  left: 50%;
  bottom: 12px;
  transform: translateX(-50%);
  display: flex;
  justify-content: center;
  opacity: 0;
  transition: opacity .25s ease, transform .25s ease;
  pointer-events: none;
  z-index: 3;
}

.product-card:hover .product-quick {
  opacity: 1;
  transform: translate(-50%, 0);
  pointer-events: auto;
}

.product-cta form button {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%; /* căn nút full width container */
}
.product-card-inner .badge {
    position: absolute;
    z-index: 5 !important;
}

</style>

@endpush
@endonce


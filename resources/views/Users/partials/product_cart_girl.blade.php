@props(['product'])

@php
  $imgMain  = product_main_src($product);
  $imgHover = product_hover_src($product);
  $final    = product_final_price($product);
  $orig     = (float)($product->price ?? 0);
  $hasSale  = $final > 0 && $final < $orig;
  $isNew    = optional($product->created_at)->gt(now()->subDays(30));
@endphp
@props(['product', 'isInWishlist' => false])

<div class="col-lg-4">
  <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
    <div class="product-item-inner border rounded">
      <div class="product-item-inner-item position-relative">
        <div class="product-thumb">
          <img src="{{ $imgMain }}"  class="main-img"  alt="{{ $product->product_name }}">
          @if($imgHover)
            <img src="{{ $imgHover }}" class="hover-img" alt="{{ $product->product_name }}">
          @endif
        </div>

        @if($hasSale)
          <span class="badge bg-danger position-absolute top-0 end-0 m-2">Sale</span>
        @elseif($isNew)
          <span class="badge bg-success position-absolute top-0 start-0 m-2">New</span>
        @endif

        <div class="product-quick">
          <a href="{{ route('users.products.show', $product->slug ?? $product->product_id) }}"
             class="btn btn-light btn-sm rounded-pill d-flex align-items-center justify-content-center">
            <i class="fa fa-eye me-1"></i> Xem nhanh
          </a>
        </div>
      </div>

      <div class="text-center rounded-bottom p-4">
        @if(!empty($product->category))
          <a href="{{ route('users.products.byCategory', $product->category->slug ?? $product->category_id) }}"
             class="d-block mb-2 text-muted small">{{ $product->category->category_name }}</a>
        @endif

        <a href="{{ route('users.products.show', $product->slug ?? $product->product_id) }}" class="d-block h5">
          {{ $product->product_name }}
        </a>

        @if($hasSale)
          <del class="me-2 fs-6 text-muted">{{ number_format($orig, 0, ',', '.') }}₫</del>
          <span class="text-primary fs-5">{{ number_format($final, 0, ',', '.') }}₫</span>
        @else
          <span class="text-primary fs-5">{{ number_format($final, 0, ',', '.') }}₫</span>
        @endif
      </div>
    </div>

    <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
     <form action="{{ route('users.cart.add', ['product' => $product->slug]) }}" method="POST" class="mb-4">
        @csrf
        <input type="hidden" name="quantity" value="1">
        <button type="submit" class="btn btn-primary border-secondary rounded-pill py-2 px-4 d-inline-flex align-items-center gap-2 d-block mx-auto">
          <i class="fas fa-shopping-cart"></i>
          <span>Thêm vào giỏ</span>
        </button>
      </form>
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex">
          <i class="fas fa-star text-primary"></i><i class="fas fa-star text-primary"></i>
          <i class="fas fa-star text-primary"></i><i class="fas fa-star text-primary"></i>
          <i class="fas fa-star"></i>
        </div>
        <div class="d-flex">
   <button type="button"
          class="btn btn-light rounded-circle btn-action js-wishlist-btn {{ $isInWishlist ? 'active' : '' }}"
          data-url="{{ route('users.wishlist.toggle', $product->slug) }}"
          aria-pressed="{{ $isInWishlist ? 'true' : 'false' }}"
          title="{{ $isInWishlist ? 'Bỏ yêu thích' : 'Thêm yêu thích' }}">
    <i class="fas fa-heart"></i>
  </button>
<form action="{{ route('users.compare.add', $product) }}" method="POST" class="m-0 p-0 d-inline-block">
  @csrf
  <button type="submit"
          class="btn btn-light rounded-circle btn-action"
          title="So sánh sản phẩm">
    <i class="fas fa-random"></i>
  </button>
</form>




        </div>
      </div>
    </div>
  </div>
</div>
@push('styles')
<style>
    .product-thumb{position:relative;height:260px;overflow:hidden;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:center}
.product-thumb img{position:absolute;inset:0;margin:auto;max-width:100%;max-height:100%;object-fit:contain;object-position:center;transition:opacity .25s}
.product-thumb .main-img{opacity:1;z-index:1}
.product-thumb .hover-img{opacity:0;z-index:2}
.product-thumb:hover .main-img{opacity:0}
.product-thumb:hover .hover-img{opacity:1}

.product-quick{display:flex;justify-content:center;opacity:0;transition:opacity .25s,transform .25s;pointer-events:none;z-index:3}
.product-item {
    position: relative;
    z-index: 1;
}

.product-item:hover {
    z-index: 10; 
}
.product-item:hover .product-quick{opacity:1;transform:translate(-50%,0);pointer-events:auto;}
.btn-heart i {
  transition: all 0.3s ease;
}
.btn-heart.active {
  background-color: #ff6a00; /* nền đỏ/cam */
  color: #fff;               /* icon trắng */
  border-radius: 50%;
  padding: 6px;
}
.product-badge,
.product-item-inner-item .badge {
    z-index: 5 !important;
    position: absolute;
}

</style>

   @endpush
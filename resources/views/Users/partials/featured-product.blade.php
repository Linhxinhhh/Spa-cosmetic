@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    if (!function_exists('spa_prod_price')) {
        function spa_prod_price($product) {
            $orig = $product->price_original ?? $product->price ?? 0;
            $sale = $product->price_sale ?? 0;
            return ($sale && $sale < $orig) ? $sale : $orig;
        }
    }
    if (!function_exists('spa_prod_discount')) {
        function spa_prod_discount($product) {
            $orig = $product->price_original ?? $product->price ?? 0;
            $final = spa_prod_price($product);
            return ($orig > 0 && $final < $orig) ? round(100 - ($final / $orig * 100)) : 0;
        }
    }
    if (!function_exists('spa_prod_img')) {
        function spa_prod_img($p) {
            $src = $p->thumbnail ?: $p->image_main;
            if (!$src) return asset('images/product-placeholder.jpg');
            if (Storage::disk('public')->exists($src)) return Storage::url($src);
            if (Str::startsWith($src, ['http://','https://','//'])) return $src;
            return asset($src);
        }
    }
@endphp

<!-- Spa Product Offers -->
<div class="container-fluid bg-light py-5">
  <div class="container">

   

    @if(!$productLeft && !$productRight)
      <div class="alert alert-warning mb-0">
        Chưa có sản phẩm nổi bật (cần ít nhất 1 sản phẩm <code>is_featured=1</code> và có <code>slug</code>).
      </div>
    @else
      <div class="row g-4">

        {{-- Banner trái --}}
        @if($productLeft && !empty($productLeft->slug))
        <div class="col-lg-6">
          <a href="{{ route('users.products.show', ['product' => $productLeft->slug]) }}"
             class="d-flex align-items-center justify-content-between border bg-white rounded-4 p-4 shadow-sm offer-card w-100 text-decoration-none">
            <div class="pe-3">
              <p class="text-muted mb-2">Sản phẩm nổi bật</p>
              <h3 class="text-primary fw-bold mb-1">{{ $productLeft->product_name }}</h3>
              @php $disc = spa_prod_discount($productLeft); @endphp
              <div class="d-flex align-items-end gap-2">
                @if($disc > 0)
                  <h1 class="display-5 text-secondary mb-0">
                    {{ $disc }}% <span class="text-primary fw-normal">OFF</span>
                  </h1>
                @endif
                <div class="ms-2">
                  <div class="h5 text-primary fw-bold mb-0">
                    {{ number_format(spa_prod_price($productLeft), 0, ',', '.') }}đ
                  </div>
                  @if($disc > 0)
                    <small class="text-muted text-decoration-line-through">
                      {{ number_format($productLeft->price_original ?? $productLeft->price, 0, ',', '.') }}đ
                    </small>
                  @endif
                </div>
              </div>
              <div class="mt-3">
                <span class="btn btn-primary btn-sm rounded-3">Mua ngay</span>
              </div>
            </div>
            <img src="{{ spa_prod_img($productLeft) }}" alt="{{ $productLeft->product_name }}"
                 class="img-fluid rounded-3 object-fit-cover" style="width:260px;height:180px;">
          </a>
        </div>
        @endif

        {{-- Banner phải --}}
        @if($productRight && !empty($productRight->slug))
        <div class="col-lg-6">
          <a href="{{ route('users.products.show', ['product' => $productRight->slug]) }}"
             class="d-flex align-items-center justify-content-between border bg-white rounded-4 p-4 shadow-sm offer-card w-100 text-decoration-none">
            <div class="pe-3">
              <p class="text-muted mb-2">Sản phẩm nổi bật</p>
              <h3 class="text-primary fw-bold mb-1">{{ $productRight->product_name }}</h3>
              @php $discR = spa_prod_discount($productRight); @endphp
              <div class="d-flex align-items-end gap-2">
                @if($discR > 0)
                  <h1 class="display-5 text-secondary mb-0">
                    {{ $discR }}% <span class="text-primary fw-normal">OFF</span>
                  </h1>
                @endif
                <div class="ms-2">
                  <div class="h5 text-primary fw-bold mb-0">
                    {{ number_format(spa_prod_price($productRight), 0, ',', '.') }}đ
                  </div>
                  @if($discR > 0)
                    <small class="text-muted text-decoration-line-through">
                      {{ number_format($productRight->price_original ?? $productRight->price, 0, ',', '.') }}đ
                    </small>
                  @endif
                </div>
              </div>
              <div class="mt-3">
                <span class="btn btn-outline-primary btn-sm rounded-3">Xem chi tiết</span>
              </div>
            </div>
            <img src="{{ spa_prod_img($productRight) }}" alt="{{ $productRight->product_name }}"
                 class="img-fluid rounded-3 object-fit-cover" style="width:260px;height:180px;">
          </a>
        </div>
        @endif

      </div>
    @endif
  </div>
</div>

<style>
  .offer-card{ transition:.2s ease; }
  .offer-card:hover{ transform:translateY(-3px); box-shadow:0 10px 22px rgba(0,0,0,.08)!important; }
  .object-fit-cover{ object-fit:cover; }
</style>

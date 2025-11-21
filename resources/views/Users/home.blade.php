@extends('Users.layouts.home')



@section('content')
@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('product_img')) {
    function product_img($p) {
        $path = $p->thumbnail ?? ($p->images ?? null);
        // nếu images là JSON/mảng -> lấy phần tử đầu
        if (is_string($path) && Str::startsWith(trim($path), '[')) {
            $arr = json_decode($path, true);
            $path = is_array($arr) && $arr ? $arr[0] : null;
        } elseif (is_array($path)) {
            $path = $path[0] ?? null;
        }
        if (!$path) return asset('images/placeholder-4x3.jpg');
        if (Str::startsWith($path, ['http://','https://','//'])) return $path;

        $path = ltrim($path, '/');
        $path = preg_replace('#^(storage/|public/)#', '', $path);
        return Storage::disk('public')->exists($path) ? Storage::url($path) : asset($path);
    }
}
@endphp





<div class="header-carousel owl-carousel px-0">
  @foreach($bannersTop as $banner)
    <div class="header-carousel-item">
      <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="banner-full">
    </div>
  @endforeach
</div>



    <!-- Carousel End -->

    <!-- Searvices Start -->
    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-6 col-md-4 col-lg-2 border-start border-end wow fadeInUp" data-wow-delay="0.1s">
                <div class="p-4">
                    <div class="d-inline-flex align-items-center">
                        <i class="fa fa-sync-alt fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Hoàn hàng miễn phí</h6>
                            <p class="mb-0">Đảm bảo hoàn tiền trong 30 ngày</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.2s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fab fa-telegram-plane fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Miễn phí vận chuyển</h6>
                            <p class="mb-0">Miễn phí vận chuyển cho tất cả đơn hàng</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.3s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-life-ring fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Hỗ trợ 24/7</h6>
                            <p class="mb-0">Chúng tôi hỗ trợ bạn mọi lúc </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.4s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Cơ hội nhận thẻ quà tặng</h6>
                            <p class="mb-0">Nhận thẻ quà tặng cho hóa đơn từ 500.000</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.5s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lock fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Thanh toán an toàn</h6>
                            <p class="mb-0">Chúng tôi coi trong sự an toàn của bạn</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.6s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-blog fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Dịch vụ trực tuyến</h6>
                            <p class="mb-0">Miễn phí trả hàng trong 30 ngày</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Searvices End -->

    <!-- Products Offer Start -->
   
    <!-- Products Offer End -->


 @include('Users.partials.featured-product', [
    'featuredProducts' => $featuredProducts,
    'productLeft'  => $productLeft,
    'productRight' => $productRight
])

    <!-- Our Products Start -->
  {{-- ========== SẢN PHẨM (HOME) ========== --}}
<div class="container-fluid product py-5">
  <div class="container py-5">
    <div class="tab-class">

      {{-- Header + Nav --}}
      <div class="row g-4 align-items-end">
        <div class="col-lg-4 text-start">
          <h1 class="mb-0">Sản phẩm</h1>
        </div>

        <div class="col-lg-8 text-end">
          <ul class="nav nav-pills d-inline-flex text-center mb-0">
            <li class="nav-item mb-4">
              <a class="nav-link d-flex mx-2 py-2 bg-light rounded-pill active"
                 data-bs-toggle="pill" href="#tab-1">
                <span class="text-dark px-3">Tất cả</span>
                <span class="badge bg-primary ms-2">{{ $productsAll->count() ?? 0 }}</span>
              </a>
            </li>
            <li class="nav-item mb-4">
              <a class="nav-link d-flex mx-2 py-2 bg-light rounded-pill"
                 data-bs-toggle="pill" href="#tab-2">
                <span class="text-dark px-3">Hàng mới về</span>
                <span class="badge bg-primary ms-2">{{ $productsNew->count() ?? 0 }}</span>
              </a>
            </li>
            <li class="nav-item mb-4">
              <a class="nav-link d-flex mx-2 py-2 bg-light rounded-pill"
                 data-bs-toggle="pill" href="#tab-3">
                <span class="text-dark px-3">Nổi bật</span>
                <span class="badge bg-primary ms-2">{{ $productsFeatured->count() ?? 0 }}</span>
              </a>
            </li>
            <li class="nav-item mb-4">
              <a class="nav-link d-flex mx-2 py-2 bg-light rounded-pill"
                 data-bs-toggle="pill" href="#tab-4">
                <span class="text-dark px-3">Bán chạy</span>
                <span class="badge bg-primary ms-2">{{ $productsBest->count() ?? 0 }}</span>
              </a>
            </li>
          </ul>
        </div>
      </div>

      {{-- Nội dung các tab --}}
      <div class="tab-content mt-4">

        {{-- TẤT CẢ --}}
        <div id="tab-1" class="tab-pane fade show active p-0">
          <div class="row g-4">
            @forelse($productsAll as $product)
              @include('Users.partials.product-card', ['product' => $product])
            @empty
              <div class="col-12"><p class="text-center py-5 mb-0">Chưa có sản phẩm đang bán.</p></div>
            @endforelse
          </div>
        </div>

        {{-- HÀNG MỚI VỀ --}}
        <div id="tab-2" class="tab-pane fade p-0">
          <div class="row g-4">
            @forelse($productsNew as $product)
              @include('Users.partials.product-card', ['product' => $product])
            @empty
              <div class="col-12"><p class="text-center py-5 mb-0">Chưa có sản phẩm mới.</p></div>
            @endforelse
          </div>
        </div>

        {{-- NỔI BẬT (đang giảm giá) --}}
        <div id="tab-3" class="tab-pane fade p-0">
          <div class="row g-4">
            @forelse($productsFeatured as $product)
              @include('Users.partials.product-card', ['product' => $product])
            @empty
              <div class="col-12"><p class="text-center py-5 mb-0">Chưa có sản phẩm nổi bật.</p></div>
            @endforelse
          </div>
        </div>

        {{-- BÁN CHẠY --}}
        <div id="tab-4" class="tab-pane fade p-0">
          <div class="row g-4">
            @forelse($productsBest as $product)
              @include('Users.partials.product-card', ['product' => $product])
            @empty
              <div class="col-12"><p class="text-center py-5 mb-0">Chưa có sản phẩm bán chạy.</p></div>
            @endforelse
          </div>
        </div>

      </div>
      {{-- /tab-content --}}

    </div>
  </div>
</div>
{{-- ========== /SẢN PHẨM (HOME) ========== --}}


    {{-- Section: danh mục hot--}}
     @include('Users.partials.hot-categories', [
    'hotCategories' => $hotCategories
])
{{-- Section: Thương hiệu --}}
<section class="brands-section container my-5">
  <div class="text-center mb-4">
    <h2 class="section-title"><span>Thương hiệu nổi bật</span></h2>
  </div>

  @if($brands->isEmpty())
    <div class="text-muted text-center">Chưa có thương hiệu.</div>
  @else
    <div class="brand-slider" style="--speed: 28s; --gap: 2rem;">
      <div class="brand-slider__track">

        {{-- Lần 1 --}}
        @foreach($brands as $brand)
          @continue(blank($brand->slug))
          <a href="{{ route('users.products.byBrand', ['brand' => $brand->slug]) }}"
             class="brand-card" title="{{ $brand->brand_name }}">
            @if(!empty($brand->logo_url))
              <img src="{{ $brand->logo_url }}" alt="Logo {{ $brand->brand_name }}" loading="lazy" width="160" height="80">
            @else
              <span class="brand-fallback">
                {{ mb_strtoupper(mb_substr($brand->brand_name,0,1,'UTF-8'),'UTF-8') }}
              </span>
            @endif
          </a>
        @endforeach

        {{-- Lần 2 (nhân đôi để chạy vô hạn mượt) --}}
        @foreach($brands as $brand)
          @continue(blank($brand->slug))
          <a href="{{ route('users.products.byBrand', ['brand' => $brand->slug]) }}"
             class="brand-card" title="{{ $brand->brand_name }}" aria-hidden="true" tabindex="-1">
            @if(!empty($brand->logo_url))
              <img src="{{ $brand->logo_url }}" alt="" loading="lazy" width="160" height="80">
            @else
              <span class="brand-fallback">
                {{ mb_strtoupper(mb_substr($brand->brand_name,0,1,'UTF-8'),'UTF-8') }}
              </span>
            @endif
          </a>
        @endforeach

      </div>
    </div>
  @endif
</section>



{{-- /Section: Thương hiệu --}}
{{-- Section: Dịch vụ --}}
 @include('Users.partials.featured-services', ['featuredServices' => $featuredServices,
 'priceCategories'  => $priceCategories,])
 



    <!-- Bestseller Products Start -->
<div class="container-fluid products pb-5">
  <div class="container products-mini py-5">
    <div class="mx-auto text-center mb-5" style="max-width: 700px;">
      <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp"
          data-wow-delay="0.1s">Sản Phẩm Bán Chạy</h4>
      <p class="mb-0 wow fadeInUp" data-wow-delay="0.2s">Những sản phẩm được mua nhiều nhất trong thời gian gần đây</p>
    </div>

    <div class="row g-4">
      @forelse($bestSellers as $product)
        @include('Users.partials.product-card', ['product' => $product])
      @empty
        <div class="col-12 text-center text-muted">Chưa có sản phẩm bán chạy.</div>
      @endforelse
    </div>

  </div>
</div>


    <!-- Bestseller Products End -->

@endsection
@push('styles')
  {{-- Font Awesome (vì bạn dùng các icon fa-*) --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-MkV..." crossorigin="anonymous" referrerpolicy="no-referrer" />

  {{-- Animate.css + WOW.js để các class .wow fadeIn* hoạt động --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  {{-- Owl Carousel 2 CSS --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"/>

  {{-- Fallback: nếu JS chưa init, vẫn hiển thị slide đầu --}}
  <style>
    .owl-carousel{display:block!important}
    .header-carousel .header-carousel-item{padding: 0 1rem;}
  </style>
@endpush

@push('scripts')
  {{-- jQuery (Owl cần jQuery) --}}
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-3fu41g..." crossorigin="anonymous"></script>

  {{-- Owl Carousel 2 JS --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
          integrity="sha512-bPs7vDi3bR..." crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  {{-- WOW.js (hiệu ứng .wow) --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
          integrity="sha512-S2r6..." crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Khởi tạo WOW (tuỳ chọn, chỉ để chạy các class .wow)
      if (typeof WOW === 'function') { new WOW().init(); }

      // Khởi tạo Owl cho header
      const $el = $('.header-carousel');
      if ($el.length && typeof $el.owlCarousel === 'function') {
        $el.owlCarousel({
          items: 1,
          loop: true,
          autoplay: true,
          autoplayTimeout: 4000,
          autoplayHoverPause: true,
          dots: true,
          nav: true,
          navText: [
            '<i class="fa fa-chevron-left"></i>',
            '<i class="fa fa-chevron-right"></i>'
          ]
        });
      } else {
        console.warn('Owl Carousel chưa sẵn sàng. Kiểm tra jQuery/CDN.');
      }
    });
  </script>
@endpush

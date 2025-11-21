    @extends('Users.layouts.home')



@section('content')
<style>
#productViewTabs .nav-link {
  width: 50px;
  height: 50px;
  padding: 0;
   display: flex !important;
    flex-direction: row !important;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

#productViewTabs .nav-link i {
  font-size: 1.1rem; /* thu nhỏ icon */
}
.product-img-wrapper {
    width: 70%;              /* thu nhỏ chiều rộng ảnh */
    height: 200px;            /* cố định chiều cao để đồng đều */
    margin: 0 auto;            /* căn giữa trong thẻ */
    overflow: hidden;          /* ẩn phần ảnh thừa */
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-img {
    width: 70%;
    height: 100%;
    object-fit: cover;         /* cắt ảnh vừa khung, không méo */
    border-radius: .5rem;      /* bo góc nhẹ */
}




</style>
<!--Hàm Hiển thị ảnh sản phẩm -->
       




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
     @include('Users.partials.featured-product', [
    'featuredProducts' => $featuredProducts,
    'productLeft'  => $productLeft,
    'productRight' => $productRight
])
    <!-- Products Offer End -->


    <!-- Shop Page Start -->
    <div class="container-fluid shop py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-3 wow fadeInUp" data-wow-delay="0.1s">
              <div class="product-categories mb-4">
                <h4>Danh Mục Sản Phẩm</h4>

                <ul class="list-unstyled">
                    @forelse($parentCategories as $parent)
                    <li class="mb-2">
                        <div class="d-flex justify-content-between align-items-center fw-bold">
                        <a href="{{ route('users.products.byCategory', $parent->slug) }}" class="text-dark">
                            <i class="fas fa-sitemap text-secondary me-2"></i>
                            {{ mb_strtoupper($parent->category_name) }}
                        </a>
                        @php
                            $total = ($parent->products_count ?? 0) + $parent->children->sum('products_count');
                            @endphp
                            <span>({{ $total }})</span>
                        </div>

                        {{-- children --}}
                        @if($parent->children->isNotEmpty())
                        <ul class="list-unstyled ms-4 mt-2">
                            @foreach($parent->children as $child)
                            <li class="mb-1">
                                <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('users.products.byCategory', $child->slug) }}" class="text-dark">
                                    {{ $child->category_name }}
                                </a>
                                <span>({{ $child->products_count }})</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </li>
                    @empty
                    <li class="text-muted">Chưa có danh mục.</li>
                    @endforelse
                </ul>
                </div>

               <div class="price mb-4">
                    <h4 class="mb-2">Lọc theo giá</h4>
                    <form method="GET" action="{{ url()->current() }}">
                        {{-- Giữ lại các filter khác đã chọn (category, brand, capacity...) --}}
                        @foreach(request()->except(['price_min','price_max','page']) as $k => $v)
                            @if(is_array($v))
                                @foreach($v as $vv)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach

                        <div class="mb-2">
                            <label class="form-label">Giá từ</label>
                            <input type="number" name="price_min" class="form-control"
                                value="{{ request('price_min') }}" placeholder="0">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Đến</label>
                            <input type="number" name="price_max" class="form-control"
                                value="{{ request('price_max') }}" placeholder="5000000">
                        </div>

                        <div class="d-flex gap-2 mt-2">
                            <button class="btn btn-sm btn-primary rounded-pill px-3">Lọc</button>
                            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Xoá</a>
                        </div>
                    </form>
                    </div>

                   <div class="product-brand mb-3">
    <h4>Thương Hiệu</h4>
    <form method="GET" action="{{ url()->current() }}">
        {{-- giữ lại các filter khác đã chọn (category, price, type...) --}}
        @foreach(request()->except(['brand_id','page']) as $k => $v)
            @if(is_array($v))
                @foreach($v as $vv)
                    <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endif
        @endforeach

        <ul class="list-unstyled">
            @foreach($brands as $brand)
                <li>
                    <label class="d-flex justify-content-between align-items-center">
                        <div>
                            <input type="checkbox" name="brand_id[]" value="{{ $brand->brand_id }}"
                                   {{ in_array($brand->brand_id,(array)request('brand_id',[])) ? 'checked' : '' }}>
                            <span class="ms-2">{{ $brand->brand_name }}</span>
                        </div>
                        <span>({{ $brand->products_count }})</span>
                    </label>
                </li>
            @endforeach
        </ul>

        <div class="mt-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Lọc</button>
            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Xoá</a>
        </div>
    </form>
</div>

                 
                    

                <div class="featured-product mb-4">
                    <h4 class="mb-3">Sản phẩm nổi bật</h4>

                    @forelse($featuredProducts as $p)
                  
                  
                        @php
                            $img  = product_img_url($p);  // thay cho product_img_url($p)
                            $orig = $p->price ?? 0;
                            $final = ($p->discount_price && $p->discount_price > 0 && $p->discount_price < $orig)
                                        ? $p->discount_price : $orig;
                        @endphp


                        <div class="featured-product-item d-flex align-items-center mb-3">
                            <a href="{{ route('users.products.show', $p->slug) }}" class="rounded me-4 d-block"
                            style="width:100px;height:100px;overflow:hidden;">
                            <img src="{{ $img ?? asset('images/placeholder-4x3.jpg') }}"
                                class="img-fluid rounded"
                                alt="{{ $p->product_name }}"
                                style="width:100%;height:100%;object-fit:cover;">
                            </a>

                            <div class="flex-grow-1">
                                @if($p->category)
                                    <a href="{{ route('users.products.byCategory', $p->category->slug) }}"
                                    class="small text-muted d-inline-block mb-1">
                                        {{ $p->category->category_name }}
                                    </a>
                                @endif

                                <a href="{{ route('users.products.show', $p->slug) }}" class="d-block fw-semibold mb-1 text-dark">
                                    {{ $p->product_name }}
                                </a>

                                {{-- rating giả định: hiển thị 4 sao xám 1 sao rỗng (tuỳ bạn thay) --}}
                                <div class="d-flex mb-2">
                                    <i class="fa fa-star text-secondary"></i>
                                    <i class="fa fa-star text-secondary"></i>
                                    <i class="fa fa-star text-secondary"></i>
                                    <i class="fa fa-star text-secondary"></i>
                                    <i class="fa fa-star"></i>
                                </div>

                                <div class="d-flex align-items-baseline gap-2">
                                    <h5 class="fw-bold mb-0">{{ number_format($final, 0, ',', '.') }} đ</h5>
                                    @if($final < $orig)
                                        <h6 class="text-danger text-decoration-line-through mb-0">
                                            {{ number_format($orig, 0, ',', '.') }} đ
                                        </h6>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Chưa có sản phẩm nổi bật.</div>
                    @endforelse

                    <div class="d-flex justify-content-center my-4">
                        <a href="{{ request()->fullUrlWithQuery(['featured' => 1]) }}"
                        class="btn btn-primary px-4 py-3 rounded-pill w-100">Xem thêm</a>
                    </div>
                </div>

              
                   
                </div>
                <div class="col-lg-9 wow fadeInUp" data-wow-delay="0.1s">
               

                <div class="rounded mb-4 position-relative">
                @if ($sidebarBanner)
                <div class="rounded mb-4 position-relative">
                    @if ($sidebarBanner)
                        <img src="{{ banner_img_url($sidebarBanner) }}" class="img-fluid rounded w-100" style="height:250px;" alt="Banner">
                        @endif
                    <div class="position-absolute rounded d-flex flex-column align-items-center justify-content-center text-center"
                        style="width:100%;height:250px;top:0;left:0;background:rgba(242,139,0,0.3);">
                        <h4 class="display-5 text-primary">{{ $sidebarBanner->title }}</h4>
                        <a href="{{ $sidebarBanner->link }}" class="btn btn-primary rounded-pill">Mua Ngay</a>
                    </div>
                </div>
                @endif

                </div>

                    <div class="row g-4">
                        <div class="col-xl-7">
                            <div class="input-group w-100 mx-auto d-flex">
                                <input type="search" class="form-control p-3" placeholder="Nhập từ khóa tìm kiếm..."
                                    aria-describedby="search-icon-1">
                                <span id="search-icon-1" class="input-group-text p-3"><i
                                        class="fa fa-search"></i></span>
                            </div>
                        </div>
                       <div class="col-xl-3 text-end">
                    <form id="sortForm" method="GET" action="{{ route('users.products.index') }}"
      class="d-flex align-items-center bg-light px-2 py-1 rounded">

    <label class="me-2 mb-0">Sắp xếp:</label>

    <select name="sort" id="sort"
            class="form-select form-select-sm border-0 bg-light p-1"
            style="width: auto;"
            onchange="this.form.submit()">
        <option value="">Mặc định</option>
        <option value="featured"   {{ request('sort')==='featured' ? 'selected' : '' }}>Nổi bật</option>
        <option value="newest"     {{ request('sort')==='newest'   ? 'selected' : '' }}>Mới nhất</option>
        <option value="sold"       {{ request('sort')==='sold'     ? 'selected' : '' }}>Bán chạy</option>
        <option value="rating"     {{ request('sort')==='rating'   ? 'selected' : '' }}>Đánh giá</option>
        <option value="price_desc" {{ request('sort')==='price_desc' ? 'selected' : '' }}>Giá: Cao → Thấp</option>
        <option value="price_asc"  {{ request('sort')==='price_asc'  ? 'selected' : '' }}>Giá: Thấp → Cao</option>
    </select>

</form>

                    </div>

                  <div class="col-lg-4 col-xl-2">
               <ul class="view-switch bg-light rounded px-3 py-1 d-flex justify-content-center gap-2"
    id="productViewTabs" role="tablist">

    <li class="nav-item" role="presentation">
        <button class="nav-link active view-btn " id="grid-tab"
                data-bs-toggle="pill" data-bs-target="#tab-5"
                type="button" role="tab" aria-controls="tab-5" aria-selected="true">
            <i class="fas fa-th"></i>
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link view-btn bg-white" id="list-tab"
                data-bs-toggle="pill" data-bs-target="#tab-6"
                type="button" role="tab" aria-controls="tab-6" aria-selected="false">
            <i class="fas fa-bars"></i>
        </button>
    </li>

</ul>

                    </div>

                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-5" role="tabpanel" aria-labelledby="grid-tab" tabindex="0">
                         
                       <div class="row g-4 product">
                            @foreach($products as $product)
                                @include('Users.partials.product_cart_girl', ['product' => $product])
                            @endforeach
                            </div>

                         </div>
                      
                <div class="tab-pane fade" id="tab-6" role="tabpanel" aria-labelledby="list-tab" tabindex="0">
                        <div class="row g-4 products-mini">
                            @foreach($products as $product)
                                @include('Users.partials.product-card', ['product' => $product])
                            @endforeach
                            </div>

                                
                            </div>
                           

                                {{-- Pagination --}}
                                <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                                    <div class="pagination d-flex justify-content-center mt-5">
                                        <a href="#" class="rounded">&laquo;</a>
                                        <a href="#" class="active rounded">1</a>
                                        <a href="#" class="rounded">2</a>
                                        <a href="#" class="rounded">3</a>
                                        <a href="#" class="rounded">4</a>
                                        <a href="#" class="rounded">5</a>
                                        <a href="#" class="rounded">6</a>
                                        <a href="#" class="rounded">&raquo;</a>
                                    </div>
                                </div>
                        </div>
                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Shop Page End -->

    <!-- Product Banner Start -->
    <!--
    <div class="container-fluid py-5">
        <div class="container pb-5">
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                    <a href="#">
                        <div class="bg-primary rounded position-relative">
                            <img src="img/product-banner.jpg" class="img-fluid w-100 rounded" alt="">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center rounded p-4"
                                style="background: rgba(255, 255, 255, 0.5);">
                                <h3 class="display-5 text-primary">EOS Rebel <br> <span>T7i Kit</span></h3>
                                <p class="fs-4 text-muted">$899.99</p>
                                <a href="#" class="btn btn-primary rounded-pill align-self-start py-2 px-4">Shop Now</a>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.2s">
                    <a href="#">
                        <div class="text-center bg-primary rounded position-relative">
                            <img src="img/product-banner-2.jpg" class="img-fluid w-100" alt="">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center rounded p-4"
                                style="background: rgba(242, 139, 0, 0.5);">
                                <h2 class="display-2 text-secondary">SALE</h2>
                                <h4 class="display-5 text-white mb-4">Get UP To 50% Off</h4>
                                <a href="#" class="btn btn-secondary rounded-pill align-self-center py-2 px-4">Shop
                                    Now</a>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
     Product Banner End -->
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endpush

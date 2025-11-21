@extends('Users.layouts.home')
@section('title', 'Giới thiệu')

@push('styles')
<style>
  .breadcrumb{
  list-style: none;
  padding: 0;
  margin: 0;
}
  .cat-link
  {
    color: #3f3f46;
  }
  .cat-link:hover
  {
    color: gray; 
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

/
.breadcrumb-chevron .breadcrumb-item + .breadcrumb-item{
  padding-left: .70rem;
}
  /* Modern Hero Section */
  .hero-about {
    position: relative;
    min-height: 520px;
    border-radius: 24px;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
  }
  
  .hero-about img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.25;
  }
  
  .hero-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #fff;
    padding: 48px 24px;
    backdrop-filter: blur(2px);
  }
  
  .hero-overlay h1 {
    font-weight: 800;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 20px rgba(0,0,0,0.2);
    line-height: 1.2;
  }
  
  .hero-overlay .lead {
    font-size: 1.15rem;
    line-height: 1.7;
    opacity: 0.95;
    text-shadow: 0 1px 10px rgba(0,0,0,0.15);
  }
  
  .hero-btns .btn {
    border-radius: 14px;
    padding: 0.875rem 1.75rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }
  
  .hero-btns .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  }

  /* Section Styling */
  .section {
    padding: 72px 0;
  }
  
  .section-title {
    font-weight: 800;
    letter-spacing: -0.5px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  .lead {
    font-size: 1.1rem;
    color: #475467;
    line-height: 1.8;
  }

  /* Modern KPI Cards */
  .kpi-card {
    border: none;
    border-radius: 20px;
    padding: 28px 20px;
    background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.08);
    position: relative;
    overflow: hidden;
  }
  
  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transform: scaleX(0);
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  .kpi-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.2);
  }
  
  .kpi-card:hover::before {
    transform: scaleX(1);
  }
  
  .kpi-card h3 {
    font-weight: 800;
    margin: 0;
    font-size: 2.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  .kpi-card p {
    margin: 8px 0 0;
    color: #6b7785;
    font-weight: 500;
    font-size: 0.95rem;
  }

  /* Feature Cards */
  .feature {
    border: none;
    border-radius: 18px;
    background: #fff;
    padding: 24px;
    height: 100%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  }
  
  .feature:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
  }
  
  .feature .icon {
    width: 32px;
    height: 32px;
    margin-right: 12px;
    filter: brightness(0.9);
  }

  /* Service & Product Cards */
  .svc-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    background: #fff;
    height: 100%;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
  }
  
  .svc-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
  }
  
  .svc-card .thumb {
    height: 200px;
    object-fit: cover;
    width: 100%;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    align-items: center;
  }
  
  .svc-card:hover .thumb {
    transform: scale(1.08);
  }
  
  .svc-card .body {
    padding: 20px;
  }
  
  .svc-card .btn {
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  /* Testimonial Cards */
  .testi {
    border: none;
    border-radius: 18px;
    background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
    padding: 24px;
    height: 100%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.08);
    position: relative;
  }
  
  .testi:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
  }
  
  .testi::before {
    content: '"';
    position: absolute;
    top: -10px;
    left: 20px;
    font-size: 80px;
    color: #667eea;
    opacity: 0.1;
    font-family: Georgia, serif;
    line-height: 1;
  }
  
  .stars {
    color: #ffa500;
    font-size: 1.1rem;
    letter-spacing: 2px;
  }

  /* Modern CTA Strip */
  .cta-strip {
    border-radius: 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 48px 36px;
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
  }
  
  .cta-strip::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 8s ease-in-out infinite;
  }
  
  @keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
  }
  
  .cta-strip h3 {
    font-weight: 800;
    font-size: 1.75rem;
  }
  
  .cta-strip .btn {
    border-radius: 14px;
    font-weight: 600;
    padding: 0.875rem 1.75rem;
    transition: all 0.3s ease;
  }
  
  .cta-strip .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  }

  /* FAQ Accordion */
  .faq details {
    border: none;
    border-radius: 16px;
    padding: 20px 24px;
    background: #fff;
    margin-bottom: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  }
  
  .faq details:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,0.1);
  }
  
  .faq details[open] {
    background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
    box-shadow: 0 6px 24px rgba(102, 126, 234, 0.12);
  }
  
  .faq summary {
    font-weight: 700;
    cursor: pointer;
    color: #1a202c;
    transition: color 0.3s ease;
    list-style: none;
    position: relative;
    padding-right: 30px;
  }
  
  .faq summary::-webkit-details-marker {
    display: none;
  }
  
  .faq summary::after {
    content: '+';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 300;
    transition: all 0.3s ease;
  }
  
  .faq details[open] summary::after {
    content: '−';
    transform: translateY(-50%) rotate(180deg);
  }
  
  .faq summary:hover {
    color: #667eea;
  }

  /* Logo Wall */
  .logo-wall img {
    height: 52px;
    object-fit: contain;
    filter: grayscale(1);
    opacity: 0.6;
    transition: all 0.3s ease;
  }
  
  .logo-wall img:hover {
    filter: grayscale(0);
    opacity: 1;
    transform: scale(1.1);
  }

  /* About List */
  .about-list li {
    margin-bottom: 14px;
    font-size: 1.05rem;
    display: flex;
    align-items: center;
    padding: 8px 0;
  }
  
  .about-list i {
    font-size: 1.3rem;
  }

  /* Story Image */
  .story-image {
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.12);
    transition: transform 0.4s ease;
  }
  
  .story-image:hover {
    transform: scale(1.02);
  }

  /* Team Member Cards */
  .team-member {
    transition: all 0.3s ease;
    padding: 12px;
    border-radius: 16px;
  }
  
  .team-member:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.1);
  }
  
  .team-member img {
    border: 3px solid #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }
  
  .team-member:hover img {
    border-color: #667eea;
    transform: scale(1.05);
  }

  /* Breadcrumb */
  .breadcrumb {
    background: transparent;
    padding: 0;
  }
  
  .breadcrumb-item a {
    color:#6c757d;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
  }
  
  .breadcrumb-item a:hover {
    color: gray;
  }

  /* Link Styling */
  a.text-decoration-none:hover {
    color:gray !important;
    transition: color 0.3s ease;
  }

  /* Responsive Adjustments */
  @media (max-width: 768px) {
    .hero-about {
      min-height: 420px;
    }
    
    .hero-overlay h1 {
      font-size: 1.75rem;
    }
    
    .section {
      padding: 48px 0;
    }
    
    .kpi-card h3 {
      font-size: 2rem;
    }
  }

  /* Smooth Scroll */
  html {
    scroll-behavior: smooth;
  }

  /* Loading Animation for KPI */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .kpi-card {
    animation: fadeInUp 0.6s ease-out backwards;
  }
  
  .kpi-card:nth-child(1) { animation-delay: 0.1s; }
  .kpi-card:nth-child(2) { animation-delay: 0.2s; }
  .kpi-card:nth-child(3) { animation-delay: 0.3s; }
  .kpi-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
@php
  $brand = config('app.name', 'LYN Clinic & Spa');

  $servicesUrl = \Route::has('users.services.index') ? route('users.services.index') : url('/dich-vu');
  $productsUrl = \Route::has('users.products.index') ? route('users.products.index') : url('/san-pham');
  $bookingUrl  = \Route::has('users.booking.create') ? route('users.booking.create') : url('/dat-lich');

  $serviceShow = function($slug) use ($servicesUrl) {
    if (\Route::has('users.services.show')) return route('users.services.show', $slug);
    return rtrim($servicesUrl,'/').'/'.$slug;
  };
  $productShow = function($slug) use ($productsUrl) {
    if (\Route::has('users.products.show')) return route('users.products.show', $slug);
    return rtrim($productsUrl,'/').'/'.$slug;
  };
@endphp

<div class="container py-4">
  {{-- Breadcrumb --}}
   <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-chevron">
            <li class="breadcrumb-item">
            <a class="cat-link" href="{{ url('/') }}">Trang chủ</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Giới thiệu</li>
        </ol>
        </nav>

  {{-- HERO --}}
  <div class="hero-about mb-5 shadow-sm">
    <img src="{{ asset('images/about/hero.jpg') }}" alt="{{ $brand }} - Giới thiệu">
    <div class="hero-overlay">
      <div class="col-lg-10">
        <h1 class="display-5 mb-3">{{ $brand }} – Đẹp từ làn da, khoẻ từ bên trong</h1>
        <p class="lead mb-4">Hệ sinh thái chăm sóc sắc đẹp toàn diện: điều trị da công nghệ cao, liệu trình spa thư giãn và hệ sản phẩm chăm sóc tại nhà đạt chuẩn.</p>
        <div class="hero-btns d-flex flex-wrap gap-3 justify-content-center">
          <a class="btn btn-light text-dark" href="{{ $servicesUrl }}"><i class="bi bi-stars me-2"></i>Khám phá Dịch vụ</a>
          <a class="btn btn-warning text-white" href="{{ $productsUrl }}"><i class="bi bi-bag-heart-fill me-2"></i>Mua sắm Sản phẩm</a>
          <a class="btn btn-outline-light" href="{{ $bookingUrl }}"><i class="bi bi-calendar2-check me-2"></i>Đặt lịch ngay</a>
        </div>
      </div>
    </div>
  </div>

  {{-- ABOUT + USP --}}
  <section class="section">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <img class="w-100 story-image" src="{{ asset('dashboard/images/logos/image.png') }}" alt="Câu chuyện thương hiệu {{ $brand }}">
      </div>
      <div class="col-lg-6">
        <h2 class="section-title mb-4">Câu chuyện thương hiệu</h2>
        <p class="lead">Ra đời với sứ mệnh nâng tầm tiêu chuẩn chăm sóc sắc đẹp, {{ $brand }} kết hợp <strong>chuyên môn y khoa</strong>, <strong>công nghệ thẩm mỹ</strong> và <strong>chuỗi sản phẩm chuẩn spa</strong> để đồng hành bền vững cùng làn da Việt.</p>
        <ul class="about-list list-unstyled mb-4">
          <li><i class="bi bi-patch-check-fill text-success me-2"></i>Đội ngũ chuyên gia & kỹ thuật viên được đào tạo bài bản</li>
          <li><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Thiết bị công nghệ cao: Laser, RF, Hifu, OxyJet...</li>
          <li><i class="bi bi-leaf-fill text-success me-2"></i>Dòng sản phẩm an toàn, minh bạch thành phần</li>
          <li><i class="bi bi-shield-lock-fill text-primary me-2"></i>Quy trình chuẩn hoá – Bảo mật thông tin khách hàng</li>
        </ul>
        <div class="row g-3">
          <div class="col-6 col-md-3">
            <div class="kpi-card"><h3 id="kpiCustomers" data-val="{{ $kpis['customers'] ?? 5000 }}">0</h3><p>Khách hàng</p></div>
          </div>
          <div class="col-6 col-md-3">
            <div class="kpi-card"><h3 id="kpiYears" data-val="{{ $kpis['years'] ?? 10 }}">0</h3><p>Năm kinh nghiệm</p></div>
          </div>
          <div class="col-6 col-md-3">
            <div class="kpi-card"><h3 id="kpiClinics" data-val="{{ $kpis['clinics'] ?? 5 }}">0</h3><p>Cơ sở</p></div>
          </div>
          <div class="col-6 col-md-3">
            <div class="kpi-card"><h3 id="kpiStars" data-val="{{ $kpis['stars'] ?? 4800 }}">0</h3><p>Đánh giá 5★</p></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- DỊCH VỤ NỔI BẬT (DỮ LIỆU THẬT) --}}
  <section class="section">
    <div class="d-flex align-items-end justify-content-between mb-4">
      <h2 class="section-title mb-0">Dịch vụ nổi bật</h2>
      <a class="text-decoration-none fw-semibold" href="{{ $servicesUrl }}">Xem tất cả <i class="bi bi-arrow-right-short"></i></a>
    </div>

    <div class="row g-4">
      @forelse($featuredServices as $svc)
        @php
          $img = $svc->image_url ?? asset('images/placeholder-4x3.jpg');
          $link = $serviceShow($svc->slug);
        @endphp
        <div class="col-md-6 col-xl-3">
          <div class="svc-card">
            <a href="{{ $link }}"><img class="thumb" src="{{ $img }}" alt="{{ $svc->service_name }}"></a>
            <div class="body">
              <h6 class="mb-2">
                <a class="text-decoration-none text-dark fw-bold" href="{{ $link }}">{{ $svc->service_name }}</a>
              </h6>
              <a class="btn btn-sm btn-outline-dark" href="{{ $link }}">Tìm hiểu</a>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="alert alert-info rounded-4">Chưa có dịch vụ nổi bật.</div>
        </div>
      @endforelse
    </div>
  </section>
 
  {{--  BIỂU (DỮ LIỆU THẬT) --}}
  <section class="section">
    <div class="d-flex align-items-end justify-content-between mb-4">
      <h2 class="section-title mb-0">Sản phẩm tiêu biểu</h2>
      <a class="text-decoration-none fw-semibold" href="{{ $productsUrl }}">Xem cửa hàng <i class="bi bi-arrow-right-short"></i></a>
    </div>

    <div class="row g-4">
      @forelse($featuredProducts as $p)
        @php
          $img = $p->image_url ?? asset('images/placeholder-4x3.jpg');
          $plink = $productShow($p->slug);
          $price = (float) ($p->discount_price ?: $p->price);
          $priceCompare = $p->discount_price && $p->price > 0 ? (float) $p->price : null;
        @endphp
        <div class="col-6 col-md-4 col-xl-3">
          <div class="svc-card h-100">
            <a href="{{ $plink }}"><img class="thumb" src="{{ $img }}" alt="{{ $p->product_name }}"></a>
            <div class="body">
              <h6 class="mb-2">
                <a class="text-decoration-none text-dark fw-bold" href="{{ $plink }}">{{ $p->product_name }}</a>
              </h6>
              <div>
                <span class="text-danger fw-bold fs-5">{{ number_format($price, 0, ',', '.') }} đ</span>
                @if($priceCompare)
                  <small class="text-muted text-decoration-line-through ms-1">
                    {{ number_format($priceCompare, 0, ',', '.') }} đ
                  </small>
                @endif
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="alert alert-info rounded-4">Chưa có sản phẩm nổi bật.</div>
        </div>
      @endforelse
    </div>
  </section>

  {{-- THƯƠNG HIỆU ĐỐI TÁC (NẾU CÓ) --}}
  @if(!empty($brands) && count($brands))
    <section class="section">
      <h2 class="section-title mb-4">Thương hiệu đối tác</h2>
      <div class="logo-wall d-flex flex-wrap gap-5 align-items-center justify-content-center">
        @foreach($brands as $b)
          <img src="{{ $b->image_url ?? asset('images/brands/placeholder.png') }}" alt="{{ $b->brand_name }}">
        @endforeach
      </div>
    </section>
  @endif

  {{-- CHUYÊN GIA & ĐÁNH GIÁ --}}
  <section class="section">
    <div class="row g-4">
      <div class="col-lg-6">
        <h2 class="section-title mb-4">Đội ngũ chuyên gia</h2>
        <div class="team-member d-flex gap-3 align-items-center mb-3">
          <img class="rounded-circle" src="{{ asset('images/team/doctor1.jpg') }}" alt="Bác sĩ da liễu" width="72" height="72">
          <div>
            <strong class="d-block fs-5">Bác sĩ A</strong>
            <div class="text-muted">Da liễu | 10+ năm kinh nghiệm</div>
          </div>
        </div>
        <div class="team-member d-flex gap-3 align-items-center mb-3">
          <img class="rounded-circle" src="{{ asset('images/team/doctor2.jpg') }}" alt="Chuyên gia chăm sóc da" width="72" height="72">
          <div>
            <strong class="d-block fs-5">Chuyên gia B</strong>
            <div class="text-muted">Chăm sóc da | 7+ năm kinh nghiệm</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <h2 class="section-title mb-4">Khách hàng nói gì?</h2>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="testi h-100">
              <div class="stars mb-2">★★★★★</div>
              <p class="mb-2">Mụn viêm giảm rõ rệt sau 4 tuần. Quy trình kỹ và rất sạch!</p>
              <small class="text-muted fw-semibold">— Thuỳ D., 24 tuổi</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="testi h-100">
              <div class="stars mb-2">★★★★★</div>
              <p class="mb-2">Liệu trình thư giãn tuyệt, sản phẩm về nhà dùng hợp da.</p>
              <small class="text-muted fw-semibold">— Minh P., 29 tuổi</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- CTA --}}
  <section class="section">
    <div class="cta-strip d-flex flex-column flex-lg-row align-items-center justify-content-between gap-4">
      <div style="position: relative; z-index: 1;">
        <h3 class="mb-2">Sẵn sàng cho hành trình làn da khoẻ đẹp?</h3>
        <div class="opacity-90 fs-6">Đặt lịch tư vấn miễn phí cùng chuyên gia của {{ $brand }} ngay hôm nay.</div>
      </div>
      <div class="d-flex gap-3" style="position: relative; z-index: 1;">
        <a class="btn btn-light text-dark" href="tel:18006778"><i class="bi bi-telephone-fill me-2"></i> 1800 6778</a>
        <a class="btn btn-dark" href="{{ $bookingUrl }}"><i class="bi bi-calendar2-check me-2"></i> Đặt lịch</a>
      </div>
    </div>
  </section>

  {{-- FAQ --}}
  <section class="section faq">
    <h2 class="section-title mb-4">Câu hỏi thường gặp</h2>
    <div class="row g-3">
      <div class="col-lg-6">
        <details>
          <summary>Thời gian một buổi liệu trình kéo dài bao lâu?</summary>
          <div class="mt-3 text-muted">Tuỳ dịch vụ, thường từ 45–90 phút. Thông tin chi tiết hiển thị trên từng dịch vụ.</div>
        </details>
      </div>
      <div class="col-lg-6">
        <details>
          <summary>Sản phẩm bán tại cửa hàng có chính hãng không?</summary>
          <div class="mt-3 text-muted">Chúng tôi cam kết sản phẩm chính hãng, nguồn gốc rõ ràng và hoá đơn đầy đủ.</div>
        </details>
      </div>
      <div class="col-lg-6">
        <details>
          <summary>Da nhạy cảm có dùng được liệu trình công nghệ cao?</summary>
          <div class="mt-3 text-muted">Bác sĩ sẽ thăm khám & cá nhân hoá phác đồ phù hợp, đảm bảo an toàn cho làn da nhạy cảm.</div>
        </details>
      </div>
      <div class="col-lg-6">
        <details>
          <summary>Làm sao để đặt lịch và đổi lịch?</summary>
          <div class="mt-3 text-muted">Bạn đặt lịch trực tuyến tại trang Đặt lịch, hoặc gọi hotline 1800 6778. Vui lòng báo trước 24h nếu muốn đổi lịch.</div>
        </details>
      </div>
    </div>
  </section>
</div>
@endsection

@push('scripts')
<script>
// Counter mini đọc từ data-val (server truyền qua $kpis)
document.addEventListener('DOMContentLoaded', function(){
  const nodes = ['#kpiCustomers','#kpiYears','#kpiClinics','#kpiStars']
    .map(sel => document.querySelector(sel))
    .filter(Boolean);

  nodes.forEach(el => {
    const target = parseInt(el.dataset.val || '0', 10);
    const dur = 2000, fps = 30, steps = Math.max(1, Math.ceil(dur/(1000/fps)));
    let i = 0, inc = target/steps;
    const timer = setInterval(()=>{
      i++; const v = Math.round(inc * i);
      el.textContent = v.toLocaleString('vi-VN');
      if(i >= steps){ el.textContent = target.toLocaleString('vi-VN'); clearInterval(timer); }
    }, 1000/fps);
  });
});
</script>

{{-- JSON-LD LocalBusiness (chỉnh sửa thông tin thật của bạn) --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "HealthAndBeautyBusiness",
  "name": "{{ $brand }}",
  "image": "{{ asset('images/about/hero.jpg') }}",
  "telephone": "+84-1800-6778",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "123 Trần Hưng Đạo",
    "addressLocality": "TP. Hồ Chí Minh",
    "postalCode": "700000",
    "addressCountry": "VN"
  },
  "url": "{{ url('/') }}"
}
</script>
@endpush
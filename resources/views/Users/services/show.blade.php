@extends('Users.servicehome')
@section('title',$service->service_name)

@section('content')
<div class="container py-4">


<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('users.services.index') }}"class="cat-link">Dịch vụ</a>
        </li>

        @if($service->category)
        
         <li class="breadcrumb-item">
    <a href="{{ route('users.services.byCategory', $service->category->slug) }}" class="cat-link">
        {{ $service->category->category_name }}
    </a>
    
</li>
  <li class="breadcrumb-item active" aria-current="page">
            {{ $service->service_name }}
        </li>
        </li>
        @endif

      
    </ol>
</nav>



  <!-- Service Detail Section -->
  <div class="row g-4 mb-5">
    <!-- Image -->
    <div class="col-lg-6">
      <div class="position-relative overflow-hidden rounded-3 shadow-sm" style="height: 450px;">
        <img class="w-100 h-100 object-fit-cover" 
             src="{{ asset('storage/'.$service->thumbnail) }}" 
             alt="{{ $service->service_name }}">
      </div>
    </div>

    <!-- Service Info -->
    <div class="col-lg-6">
      <div class="bg-white rounded-3 shadow-sm p-4 h-100">
        <h1 class="h2 fw-bold mb-3">{{ $service->name }}</h1>
        
        <!-- Meta Info -->
        <div class="d-flex align-items-center gap-3 mb-4 text-muted">
          @if($service->category)
          <span class="d-flex align-items-center gap-1">
            <svg width="16" height="16" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
              <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z"/>
              <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1zm0 5.586 7 7L13.586 9l-7-7H2v4.586z"/>
            </svg>
            {{ $service->category->name }}
          </span>
          @endif
          <span class="d-flex align-items-center gap-1">
            <svg width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
              <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
              <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
            </svg>
            {{ $service->duration }} phút
          </span>
        </div>

        <!-- Pricing -->
        <div class="mb-4">
          @php
            // Nếu model có accessor final_price thì ưu tiên
            if (method_exists($service,'getFinalPriceAttribute') && $service->final_price > 0) {
                $display = (float) $service->final_price;
                $compare = (float) ($service->price_original ?? 0);
            } else {
                $p   = (float) ($service->price ?? 0);
                $po  = (float) ($service->price_original ?? 0);
                $ps  = (float) ($service->price_sale ?? 0);

                // Giá sẽ hiển thị
                $display = $p > 0 ? $p : ($ps > 0 ? $ps : $po);

                // Giá để gạch so sánh (nếu có)
                $compare = 0;
                if ($display == $ps && $po > $ps) {
                    $compare = $po;
                } elseif ($display == $p && $po > $p) {
                    $compare = $po;
                }
            }

            $hasCompare = $compare > 0 && $display > 0 && $compare > $display;
            $discount   = $hasCompare ? (100 - round($display * 100 / $compare)) : null;
          @endphp

          <div class="d-flex align-items-baseline gap-2 mb-2">
            <h3 class="h2 fw-bold text-danger mb-0">{{ number_format($display,0,',','.') }}đ</h3>
            @if($hasCompare)
              <del class="h5 text-muted mb-0">{{ number_format($compare,0,',','.') }}đ</del>
            @endif
          </div>
          
          @if($hasCompare)
            <span class="badge bg-success bg-gradient px-3 py-2">
              <svg width="14" height="14" fill="currentColor" class="bi bi-lightning-fill" viewBox="0 0 16 16">
                <path d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641l2.5-8.5z"/>
              </svg>
              Giảm {{ $discount }}%
            </span>
          @endif
        </div>

        <!-- Description -->
        <div class="mb-4">
          <h5 class="fw-semibold mb-3">Mô tả dịch vụ</h5>
          <div class="text-muted lh-lg">{!! nl2br(e($service->description)) !!}</div>
        </div>

        <!-- CTA Button -->
        <div class="d-grid gap-2">
          <a href="{{ route('users.booking.create',['service'=>$service->slug]) }}" 
             class="btn btn-primary btn-lg shadow-sm">
            <svg width="20" height="20" fill="currentColor" class="bi bi-calendar-check me-2" viewBox="0 0 16 16">
              <path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
              <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
            </svg>
            Đặt lịch ngay
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Related Services Section -->
  <div class="mt-5 pt-4 border-top">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h3 class="h4 fw-bold mb-0">Dịch vụ liên quan</h3>
      <a href="{{ route('users.services.index') }}" class="text-decoration-none">
        Xem tất cả
        <svg width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
        </svg>
      </a>
    </div>

    <div class="row g-4">
      @foreach($related as $r)
      <div class="col-6 col-md-4 col-lg-3">
        <a class="text-decoration-none" href="{{ route('users.services.show',$r->slug) }}">
          <div class="card h-100 border-0 shadow-sm hover-lift transition-all">
            <div class="position-relative overflow-hidden">
              <img class="card-img-top" 
                   style="height: 200px; object-fit: cover;"
                   src="{{ asset('storage/' . $r->thumbnail) }}"
                   alt="{{ $r->name }}">
              <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-white text-dark">{{ $r->duration ?? '' }} phút</span>
              </div>
            </div>
            <div class="card-body">
              <h6 class="card-title text-dark fw-semibold mb-2 text-truncate">{{ $r->name }}</h6>
              <div class="d-flex align-items-center justify-content-between">
                <span class="fw-bold text-danger">{{ number_format($r->final_price,0,',','.') }}đ</span>
                <svg width="20" height="20" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                </svg>
              </div>
            </div>
          </div>
        </a>
      </div>
      @endforeach
    </div>
  </div>
</div>

<style>
.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}
.transition-all {
  transition: all 0.3s ease;
}
.object-fit-cover {
  object-fit: cover;
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

/* Nới khoảng bên trái item sau */
.breadcrumb-chevron .breadcrumb-item + .breadcrumb-item{}
</style>
@endsection
@extends('Users.servicehome')
@section('title',$service->service_name)

@section('content')
<div class="container py-4">
    @php
        $images = $service->images ?? collect();
        $firstUrl  = optional($images->first())->image_url;
        $secondUrl = optional($images->skip(1)->first())->image_url;
        $first  = $firstUrl ? src_img_get($firstUrl) : src_img_get($service->thumbnail);
        $second = $secondUrl ? src_img_get($secondUrl) : null;
    @endphp

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb ">
            <li class="breadcrumb-item">
                <a href="{{ route('users.services.index') }}" class="cat-link">
                    <i class="bi bi-house-door me-1"></i>Dịch vụ
                </a>
            </li>
            @if($service->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('users.services.byCategory', $service->slug) }}" class="cat-link">
                        {{ $service->category->category_name }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $service->service_name }}
                </li>
            @endif
        </ol>
    </nav>

    <!-- Service Detail Section -->
    <div class="row g-4 mb-5">
        <!-- Image Gallery -->
        <div class="col-lg-6">
            <div class="sticky-top" style="top: 20px;">
                <div class="position-relative overflow-hidden rounded-4 shadow-lg mb-3" style="height: 450px;">
                    <img class="w-100 h-100 object-fit-cover" 
                         src="{{ $first }}"
                         alt="{{ $service->service_name }}">
                    <div class="image-overlay"></div>
                </div>
                @if($second)
                    <div class="position-relative overflow-hidden rounded-4 shadow" style="height: 150px;">
                        <img class="w-100 h-100 object-fit-cover" 
                             src="{{ $second }}"
                             alt="{{ $service->service_name }}">
                    </div>
                @endif
            </div>
        </div>

        <!-- Service Info -->
        <div class="col-lg-6">
            <div class="service-info-wrapper">
                <h1 class="display-5 fw-bold mb-3 text-dark">{{ $service->service_name }}</h1>
                
                <!-- Meta Info -->
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                    @if($service->category)
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                            <i class="bi bi-tag me-1"></i>
                            {{ $service->category->category_name }}
                        </span>
                    @endif
                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                        <i class="bi bi-clock me-1"></i>
                        {{ $service->duration }} phút
                    </span>
                </div>

                <!-- Pricing Card -->
                @php
                    if (method_exists($service,'getFinalPriceAttribute') && $service->final_price > 0) {
                        $display = (float) $service->final_price;
                        $compare = (float) ($service->price_original ?? 0);
                    } else {
                        $p   = (float) ($service->price ?? 0);
                        $po  = (float) ($service->price_original ?? 0);
                        $ps  = (float) ($service->price_sale ?? 0);
                        $display = $p > 0 ? $p : ($ps > 0 ? $ps : $po);
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

                <div class="price-card bg-gradient rounded-4 p-4 mb-4 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <p class="text-muted mb-1 small">Giá dịch vụ</p>
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="display-4 fw-bold text-danger mb-0">
                                    {{ number_format($display,0,',','.') }}<small class="fs-4">đ</small>
                                </h2>
                                @if($hasCompare)
                                    <del class="h5 text-muted mb-0">{{ number_format($compare,0,',','.') }}đ</del>
                                @endif
                            </div>
                        </div>
                        @if($hasCompare)
                            <div class="discount-badge">
                                <span class="badge bg-success fs-5 px-4 py-3 rounded-pill shadow">
                                    <i class="bi bi-lightning-fill me-1"></i>
                                    -{{ $discount }}%
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- CTA Button -->
                    <a href="{{ route('users.booking.create',['service'=>$service->slug]) }}" 
                       class="btn btn-primary btn-lg w-100 py-3 rounded-pill shadow-sm btn-booking">
                        <i class="bi bi-calendar-check me-2"></i>
                        Đặt lịch ngay
                    </a>
                </div>

                <!-- Description -->
                <div class="description-card bg-white rounded-4 p-4 shadow-sm">
                    <h5 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="bi bi-file-text text-primary me-2"></i>
                        Mô tả dịch vụ
                    </h5>
                    <div class="text-muted lh-lg description-content">
                        {!! nl2br(e($service->description)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Services Section -->
    <div class="related-section mt-5 pt-5">
        <div class="section-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="h3 fw-bold mb-0">
                        <i class="bi bi-grid-3x3-gap text-primary me-2"></i>
                        Dịch vụ liên quan
                    </h3>
                </div>
                <div class="col-auto">
                    <a href="{{ route('users.services.index') }}" 
                       class="btn btn-outline-primary rounded-pill px-4">
                        Xem tất cả
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach($related as $r)
                @php
                    $relatedImages = $r->images ?? collect();
                    $relatedFirstUrl = optional($relatedImages->first())->image_url;
                    $relatedFirst = $relatedFirstUrl ? src_img_get($relatedFirstUrl) : src_img_get($r->thumbnail);
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <a class="text-decoration-none" href="{{ route('users.services.show',$r->slug) }}">
                        <div class="card service-card h-100 border-0 shadow-sm">
                            <div class="position-relative overflow-hidden card-img-wrapper">
                                <img class="card-img-top"
                                     src="{{ $relatedFirst }}"
                                     alt="{{ $r->service_name }}"
                                     style="height: 220px; object-fit: cover;">
                                <div class="card-overlay"></div>
                                @if($r->duration)
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-white text-dark shadow-sm px-3 py-2">
                                            <i class="bi bi-clock me-1"></i>{{ $r->duration }} phút
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body p-3">
                                <h6 class="card-title text-dark fw-bold mb-2 two-line-truncate">
                                    {{ $r->service_name }}
                                </h6>
                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <span class="h5 fw-bold text-danger mb-0">
                                        {{ number_format($r->price_original ?? 0,0,',','.') }}đ
                                    </span>
                                    <i class="bi bi-arrow-right-circle-fill text-primary fs-4"></i>
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
/* Variables */
:root {
    --primary-color: #0d6efd;
    --danger-color: #dc3545;
    --success-color: #198754;
    --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Breadcrumb */
.breadcrumb {
    font-size: 0.95rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: '›';
    font-size: 1.3rem;
    color: #adb5bd;
    padding: 0 0.5rem;
}

.cat-link {
    color: #495057;
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
}

.cat-link:hover {
    color: gray;
}

/* Image Effects */
.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.1) 100%);
    pointer-events: none;
}

/* Price Card */
.price-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #e9ecef;
}

/* Button Effects */
.btn-booking {
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: var(--transition);
    border: none;
}

.btn-booking:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(13, 110, 253, 0.3);
}

.btn-booking:active {
    transform: translateY(0);
}

/* Description Card */
.description-card {
    border: 1px solid #e9ecef;
}

.description-content {
    line-height: 1.8;
    font-size: 0.95rem;
}

/* Related Services Cards */
.service-card {
    border-radius: 1rem;
    transition: var(--transition);
    overflow: hidden;
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.card-img-wrapper {
    border-radius: 1rem 1rem 0 0;
    position: relative;
}

.card-img-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.05) 100%);
    opacity: 0;
    transition: var(--transition);
}

.service-card:hover .card-img-wrapper::after {
    opacity: 1;
}

.service-card .card-img-top {
    transition: var(--transition);
}

.service-card:hover .card-img-top {
    transform: scale(1.05);
}

/* Truncate Text */
.two-line-truncate {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 3em;
}

/* Section Header */
.section-header {
    border-bottom: 3px solid #e9ecef;
    padding-bottom: 1rem;
}

/* Utility */
.object-fit-cover {
    object-fit: cover;
}

/* Responsive */
@media (max-width: 991px) {
    .sticky-top {
        position: static !important;
    }
    
    .display-5 {
        font-size: 2rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
}

@media (max-width: 576px) {
    .price-card {
        padding: 1.5rem !important;
    }
    
    .discount-badge .badge {
        font-size: 1rem !important;
        padding: 0.5rem 1rem !important;
    }
}
</style>

@endsection
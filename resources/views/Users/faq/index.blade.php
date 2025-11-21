@extends('users.servicehome')
@section('title','Hỏi & Đáp')

@php
    use Illuminate\Support\Str;
    // Dữ liệu giả định cho Sidebar (bạn cần thay thế bằng dữ liệu thực từ Controller)
    $categories = ['Chăm sóc Da', 'Chăm sóc Tóc', 'Dịch vụ Spa', 'Giảm bỡ', 'Sản phẩm Bán chạy'];
    $related_posts = [
        ['title' => 'Cẩm nang dưỡng ẩm hiệu quả', 'link' => '#'],
        ['title' => 'Top 5 dịch vụ Spa hot nhất', 'link' => '#'],
        ['title' => 'Cách trị mụn sẹo rỗ', 'link' => '#'],
    ];
      $img = function ($path) {
        if (!$path) return asset('images/default-post.png');
        return Str::startsWith($path, ['http://','https://'])
            ? $path : asset('storage/'.$path);
    };
    
@endphp

@section('content')
<style>
    .root {
        --primary-color: #007bff; /* Xanh dương sáng (Blue) */
        --secondary-color: #6c757d; /* Xám tối */
        --success-color: #28a745; /* Xanh lá (Green) */
        --warning-color: #ffc107; /* Vàng (Yellow) */
        --info-color: #17a2b8; /* Xanh ngọc (Cyan) */
        --bg-light: #f8f9fa; /* Nền sáng */
        --border-color: #dee2e6;
        --b
  }
  .faq-card {
    border: 1px solid #eef0f3;
    border-radius: 16px;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }
  .faq-card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    transform: translateY(-2px);
  }
    .faq-hero .h3 {
        color: var(--blue-active); /* Màu tiêu đề nổi bật */
    }
  .faq-card .accordion-button {
    font-weight: 600;
    padding: 1.25rem 1.5rem;
    background: #fff;
    border: none;
  }
  .faq-card .accordion-button:not(.collapsed) {
    background: #f8fafc;
    color: #1e40af;
  }
  .faq-card .accordion-button::after {
    flex-shrink: 0;
  }
  /* Accordion Body */
  .faq-card .accordion-body {
    padding: 1.5rem;
    background: #fff;
  }
  /* Meta Info */
  .meta {
    font-size: 0.875rem;
color: #6b7280;
}
  /* Chips & Badges */
.faq-chip {
background: #f1f5f9;
color: #0f172a;
border-radius: 999px;
padding: 0.25rem 0.75rem;
font-size: 0.75rem;
font-weight: 600;
white-space: nowrap;
}
  .faq-status {
    border-radius: 999px;
padding: 0.25rem 0.75rem;
font-size: 0.75rem;
font-weight: 600;
    white-space: nowrap;
}
  .faq-status.ok {
    background: #ecfdf5;
    color: #065f46;
  }
  .faq-status.wait {
    background: #fff7ed;
    color: #9a3412;
  }
  /* Search Input */
  .search-inp {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
  }
  .search-inp:focus {
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
  }
  /* Admin Reply */
  .admin-reply {
    border-left: 4px solid #3b82f6;
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.25rem;
  }
  /* Asker Avatar */
  .asker-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
  }
  /* Category Header */
  .category-header {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    margin-bottom: 1rem;
  }
  /* Responsive Images in Content */
  .accordion-body img,
  .admin-reply img {
    max-width: 100%;
    height: auto;
   border-radius: 8px;
    margin: 1rem 0;
  }
  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #f8fafc;
    border-radius: 16px;
    border: 2px dashed #e5e7eb;
  }
  .empty-state i {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
  }
  /* Responsive Adjustments */
  @media (max-width: 768px) {
    .faq-card .accordion-button {
      padding: 1rem;
      font-size: 0.9rem;
    }
    .asker-avatar {
      width: 32px;
      height: 32px;
      font-size: 0.75rem;
    }
    .faq-chip,
    .faq-status {
      font-size: 0.7rem;
      padding: 0.2rem 0.5rem;
    }
  }
.faq-cover{
        display:flex;
        justify-content:center;
        align-items:center;
        /* Đảm bảo khung hình ảnh không quá lớn */
        max-height: 250px;
        overflow: hidden;
    }
    .faq-cover img{
        width: 40%; /* Lấp đầy chiều rộng khung */
        height: 250px; /* Chiều cao cố định */
        object-fit: cover;
    }
    .faq-hero {
        background: linear-gradient(180deg, #eff6ff, #fff); /* Giữ lại màu Hero cũ nếu bạn muốn */
        border-bottom: 1px solid #edf2f7;
    }
        /* Thêm style cơ bản cho Sidebar */
    .sidebar-widget {
        border: 1px solid #eef0f3;
        border-radius: 12px;
        background: #fff;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
        .sidebar-widget h5 {
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1e40af; /* Màu tiêu đề sidebar */
        border-bottom: 2px solid #eef0f3;
        padding-bottom: 0.5rem;
    }
        .sidebar-list .list-group-item {
        border: none;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        transition: background-color 0.2s;
    }
        .sidebar-list .list-group-item:hover {
        background-color: #f8fafc;
        color: #3b82f6;
    }
    /* Đảm bảo ảnh bìa chiếm 100% chiều rộng của container nó */
    .faq-cover img {
        width: 100%; 
        height: 250px;
        object-fit: cover;
    }
    /* Thẻ dịch vụ bán chạy */
    .hot-list{ display:flex; flex-direction:column; gap:18px; }

/* Card */
.svc-item{
    margin-right: auto;
  display:block;
  padding:15px;
  border:1.5px solid transparent;
  transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;

  /* THU ĐỘ RỘNG */
  width:70%;
  max-width:320px;   /* ↓ chỉnh 260–360 tuỳ ý */
      /* canh giữa nếu container rộng */
}
.svc-item:hover{
  border-color:var(--accent);
  box-shadow:0 10px 24px rgba(255,122,0,.12);
  transform: translateY(-2px);
}

/* Ảnh trên */
.svc-thumb{
  position:relative;
  
  overflow:hidden;
  background:#f3f4f6;
  aspect-ratio: 4/3;          /* Ảnh như hình mẫu */
}
.svc-thumb img{ width:100%; height:100%; object-fit:cover; }
.svc-ribbon{
  position:absolute; left:8px; bottom:8px;
  background:var(--accent);
  color:#fff; font-weight:700; font-size:12px;
  padding:4px 8px;
}

/* Thân dưới */
.svc-body{ padding-top:8px; }
.svc-price{
  color:var(--accent-2);
  font-size:20px; font-weight:800;
  margin-bottom:6px;
}
.svc-price small{ font-weight:800; }

.svc-title{
  color:#0f172a; font-weight:600; line-height:1.3;
  margin-bottom:6px;
  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}

.svc-meta{
  display:flex; align-items:center;
  gap:4px;                /* 6px -> 4px */
  color:var(--muted);
  font-size:.8rem;        /* .9rem -> .8rem */
  line-height:1.2;        /* thêm line-height gọn hơn */
  margin-bottom:4px;      /* 6px -> 4px */
}
.svc-meta i{ font-size:.85rem; }  /* icon nhỏ lại một chút */
.stars{
  font-size:.60rem;      /* kích thước chữ của sao */
  letter-spacing:.3px;   /* bớt giãn cách giữa các sao */
  line-height:1;
}
.stars .bi{              /* icon bootstrap */
  font-size:.9em;        /* thêm chút kiểm soát chi tiết */
  vertical-align:-1px;   /* căn lại cho gọn hàng */
  margin-right:1px;
}
.svc-meta .dot{
  opacity:.5;             /* .6 -> .5 */
  margin:0 2px;           /* thu hẹp khoảng cách quanh dấu */
  font-size:.8rem;
}
@media (max-width:570px){
  .svc-meta{ font-size:.75rem; gap:3px; }
  .stars{ font-size:.50rem; letter-spacing:.3px; }
}
/* Progress */
.svc-progress{
  height:4px; background:#ffe0b3; border-radius:999px; overflow:hidden;
}
.svc-progress > span{
  display:block; height:100%; background:var(--accent); border-radius:999px;
}
.svc-progress-text{
  text-align:right; color:var(--muted); font-size:.85rem; margin-top:2px;
}
        /* Responsive cho Sidebar (Ẩn Sidebar trên màn hình nhỏ) */
    @media (max-width: 991.98px) {
        .sidebar-left, .sidebar-right {
            display: none !important; /* Ẩn cả hai sidebar trên mobile/tablet */
        }
        /* Nội dung chính chiếm toàn bộ chiều rộng khi sidebar bị ẩn */
        .col-lg-6 {
            width: 100%;
        }
    }
    /* Khung ảnh nhỏ gọn cho mục Dịch vụ bán chạy */
.thumb-svc{
  width: 64px;              /* ↓ thu nhỏ theo ý bạn: 56 / 64 / 72 / 80 */
  height: 64px;
  flex: 0 0 64px;           /* giữ kích thước cố định khi dùng flex */
  border-radius: 8px;
  overflow: hidden;
}

.thumb-svc .img-cover{
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;        /* ảnh lấp đầy khung mà không méo */
}
  .heading-underline{
  font-size: 1.25rem;           
  letter-spacing: .04em;
  position: relative;
  padding-bottom: .45rem;         
}
.heading-underline::after{
  content: "";
  position: absolute;
  left: 0; bottom: 0;
  width: 64px;                    
  height: 3px;
  border-radius: 999px;
  background: linear-gradient(90deg,#f97316,#ea580c); /* cam */
}
/*  hien thi bai viet noi bat */
.popular-list{
  display:flex;
  flex-direction:column;
  gap:16px;
  padding:0 .75rem .75rem .75rem; /* tương đương p-3 */
}

.popular-item{
    display:block;
  color:inherit;
}

.popular-thumb{
  width:70%;
  aspect-ratio: 4/3;                 /* vuông 1/1 cũng được: đổi thành 1/1 nếu muốn */
  
  overflow:hidden;
  background:#f3f4f6;
}
.popular-thumb img{
  width:100%;
  height:120%;
  object-fit:cover;
  transition:transform .2s ease;
}

.popular-title{
  margin-top:.5rem;
  font-size:14px;
  line-height:1.35;
  color:#111827;
  display:-webkit-box;
  -webkit-line-clamp:2;               /* cắt 2 dòng */
  -webkit-box-orient:vertical;
  overflow:hidden;
}

/* Hover effects */
.popular-item:hover .popular-thumb img{ transform:scale(1.04); }
.popular-item:hover .popular-title{ color:#f97316; } /* cam */
/* Nếu muốn nhỏ hơn trên mobile */
@media (max-width: 576px){
  .thumb-svc{ width:56px; height:56px; }
}

</style>
{{-- Hero Section (Không đổi) --}}
<div class="faq-hero py-4">
    <div class="container-xl">
        <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
          <a href="{{ route('users.home') }}" class="text-decoration-none">
            <i class="bi bi-house-door me-1"></i>Trang chủ
          </a>
        </li>
       <li class="breadcrumb-item active">Hỏi & Đáp</li>
      </ol>
    </nav>
   <div class="d-flex align-items-center gap-3">
      <i class="bi bi-question-circle-fill text-primary fs-2"></i>
      <div>
        <h1 class="h3 mb-1 fw-bold">Câu hỏi thường gặp</h1>
        <p class="text-muted mb-0 small">Tìm câu trả lời cho các thắc mắc của bạn</p>
      </div>
    </div>
    </div>
</div>
{{-- Main Content - Chia thành 3 cột --}}
<div class="py-5">
    <div class="container-xl">
        <div class="row">
                        {{-- 1. Sidebar Trái (Danh mục, Dịch vụ nổi bật) - Chiếm 3/12 cột --}}
            <div class="col-lg-3 d-none d-lg-block sidebar-left">
                               {{-- Widget 1: Danh mục Dịch vụ --}}
                <div class="">
                    <div class="px-0 pt-2 pb-3">
                            <h3 class="heading-underline fw-bold text-uppercase m-0">
                        DỊCH VỤ
                        </h3>
                    </div>
                    <div class=" list-unstyled  ">
                       <ul class=" list-unstyled d-block py-2 px-4 small text-dark text-start hover-orange ">
                            @forelse($serviceParents as $p)
                            @php
                                $slug = $p->slug ?? null;
                                $name = $p->category_name ?? 'Danh mục';
                                $to   = !empty($slug)
                                        ? route('users.services.byCategory', ['category' => $slug])   // {category:slug}
                                        : route('users.services.index', ['category_id' => $p->category_id]); // fallback
                            @endphp
                            <li >
                                <a href="{{ $to }}" class="cat-link d-flex align-items-center justify-content-between">
                                <span class="d-inline-flex align-items-center gap-2">
                                    <span class="cat-name text-black">{{ $name }}</span>
                                </span>

                                <span class="d-inline-flex align-items-center gap-2">
                                    @isset($p->services_count)
                                    <span class="badge rounded-pill bg-light text-black">
                                        {{ $p->services_count }}
                                    </span>
                                    @endisset
                                </span>
                                </a>
                            </li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>
                
                                {{-- Widget 2: Dịch vụ Bán chạy --}}
                          <div class="hot-list">
                          <div class="px-0 pt-2 pb-3">
                            <h3 class="heading-underline fw-bold text-uppercase m-0">
                        DỊCH VỤ BÁN CHẠY
                        </h3>
                    </div>
                        <div class="">
                            @forelse($hotServices as $s)
                            @php
                            $name   = $s->name ?? $s->service_name;
                            $thumb  = $img($s->thumbnail ?? null);
                            $price  = number_format($s->price ?? 0, 0, ',', '.');
                            $rating = $s->rating_avg ?? 4.8;           // fallback
                            $count  = $s->rating_count ?? 12;          // fallback
                            $time   = $s->duration_text ?? '5 phút';   // fallback
                            $pct    = $s->sold_percent ?? 33;          // % bán / khuyến nghị (fallback)
                            @endphp
                                 <a href="{{ route('users.services.show', $s->slug) }}" class="svc-item">
                            <div class="svc-thumb">
                                <img src="{{ $thumb }}" alt="{{ $name }}">
                                <span class="svc-ribbon">TRẢI NGHIỆM</span>
                            </div>

                            <div class="svc-body">
                                <div class="svc-price">{{ $price }} <small>đ</small></div>

                                <div class="svc-title">{{ $name }}</div>

                                <div class="svc-meta">
                                <span class="stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </span>
                                <span class="muted">({{ $count }})</span>
                                <span class="dot">|</span>
                                <i class="bi bi-clock me-1"></i>{{ $time }}
                                </div>

                                <div class="svc-progress">
                                <span style="width: {{ max(0,min(100,$pct)) }}%"></span>
                                </div>
                                <div class="svc-progress-text">{{ max(0,min(100,$pct)) }}%</div>
                            </div>
                            </a>
                            @empty
                                <div class="small text-muted text-center py-3">Chưa có dữ liệu</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                      {{-- 2. Nội dung Chính (FAQ) - Chiếm 6/12 cột --}}
            <div class="col-lg-6 col-md-12">
                {{-- Search & Filter Form --}}
                <div class="card border-0 shadow-sm mb-5">
                  <div class="card-body p-4">
        <form class="row g-3" method="get">
          <div class="col-md-8">
            <div class="position-relative">
              <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
              <input
                name="q"
                value="{{ $q }}"
                class="form-control search-inp ps-5"
                placeholder="Nhập từ khóa tìm kiếm câu hỏi...">
            </div>
          </div>
          <div class="col-md-3">
            <select name="subcategory" class="form-select search-inp"
              style="width:201px ;">
              <option value="">Tất cả chuyên mục </option>
              @foreach($subcategories as $sc)
                <option value="{{ $sc }}" @selected(($sub ?? '') === $sc)>{{ $sc }}</option>
              @endforeach
            </select>
          </div>
       
        </form>
           <div class="col-md-3">
            <button style="" class="btn btn-primary w-80 ">
             
              <span class="d-none d-lg-inline ms-1">Tìm kiếm</span>
            </button>
          </div>
      </div>
                </div>
                {{-- FAQ Groups --}}
                @forelse($faqs as $group => $items)
                 @php $slug = Str::slug($group ?: 'khac'); @endphp   
      <div class="mb-5">
        {{-- Category Header --}}
        <div class="category-header">
          <i class="bi bi-folder2-open text-warning fs-5"></i>
          <h2 class="h5 mb-0 fw-semibold">{{ $group ?: 'Khác' }}</h2>
          <span class="faq-chip">{{ $items->count() }}</span>
        </div>
        {{-- Accordion --}}
        <div class="accordion accordion-flush" id="faq-{{ $slug }}">
          @foreach($items as $i => $f)
            @php
              $askName   = $f->asker_name ?? ($f->user?->name ?? 'Ẩn danh');
              $askEmail  = $f->asker_email ?? ($f->user?->email ?? null);
              $askInit   = Str::upper(Str::substr($askName, 0, 2));
              $catName   = $f->category ?: 'Khác';
              $answered  = filled($f->answer);
              $adminName = $f->answered_by_name ?? ($f->answeredBy?->name ?? 'Admin');
              $createdAt = optional($f->created_at)->format('d/m/Y H:i');
              $answeredAt= optional($f->answered_at ?? $f->updated_at)->format('d/m/Y H:i');
              $cid       = 'collapse-'.$slug.'-'.$i;
              $open      = ($loop->first && empty($q) && empty($cat));
              // Title
              $fromContact = trim((string) ($f->contact?->message ?? ''));
              $rawQuestion = trim((string) ($f->question ?? ''));
              $title = $fromContact !== '' ? $fromContact : ($rawQuestion !== '' ? $rawQuestion : $catName);
            @endphp
            <div class="accordion-item faq-card mb-3">
              {{-- Cover Image --}}
              @if($f->cover_image)
                <div class="faq-cover">
                  <img
                    src="{{ asset('storage/'.$f->cover_image) }}"
                    alt="{{ $title }}"
                    loading="lazy"
                  >
                </div>
              @endif
              {{-- Question Header --}}
              <h2 class="accordion-header">
                <button
                  class="accordion-button {{ $open ? '' : 'collapsed' }}"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#{{ $cid }}"
                  aria-expanded="{{ $open ? 'true' : 'false' }}"
                  aria-controls="{{ $cid }}"
                ><span class="asker-avatar me-3">{{ $askInit }}</span>
                  <span class="flex-fill pe-2">{{ $title }}</span>
                  <span class="d-none d-md-flex align-items-center gap-2">
                    <span class="faq-chip">
                      <i class="bi bi-folder2-open me-1"></i>{{ $catName }}
                    </span>
                    <span class="faq-status {{ $answered ? 'ok' : 'wait' }}">
                      <i class="bi {{ $answered ? 'bi-check-circle-fill' : 'bi-hourglass-split' }} me-1"></i>
                      {{ $answered ? 'Đã trả lời' : 'Chờ' }}
                    </span>
                  </span>
                </button>
              </h2>
              {{-- Answer Body --}}
              <div
                id="{{ $cid }}"
                class="accordion-collapse collapse {{ $open ? 'show' : '' }}"
                data-bs-parent="#faq-{{ $slug }}">
                   <div class="accordion-body">
                  {{-- Meta Information --}}
                  <div class="meta mb-4 d-flex flex-wrap align-items-center gap-3 pb-3 border-bottom">
                    <div>
                      <i class="bi bi-person-circle me-1"></i>
                      <strong>{{ $askName }}</strong>
                      @if($askEmail)
                        <span class="text-muted small">&lt;{{ $askEmail }}&gt;</span>
                      @endif
                    </div>
                    <div>
                      <i class="bi bi-calendar-event me-1"></i>
                      {{ $createdAt }}
                    </div>                
                    {{-- Mobile: Show category and status --}}
                    <div class="d-md-none w-100 d-flex gap-2">
                      <span class="faq-chip">
                        <i class="bi bi-folder2-open me-1"></i>{{ $catName }}
                      </span>
                      <span class="faq-status {{ $answered ? 'ok' : 'wait' }}">
                        <i class="bi {{ $answered ? 'bi-check-circle-fill' : 'bi-hourglass-split' }} me-1"></i>
                        {{ $answered ? 'Đã trả lời' : 'Chờ trả lời' }}
                      </span>
                    </div>                    
                    @if($answered)
                      <div class="d-none d-md-block">|</div>
                      <div>
                        <i class="bi bi-patch-check-fill text-success me-1"></i>
                        Trả lời: {{ $answeredAt }}
                      </div>
                    @endif
                  </div>
                  {{-- Admin Answer --}}
                  @if($answered)
                    <div class="admin-reply">
                      <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-shield-check fs-5 text-primary"></i>
                        <strong class="text-primary">Phản hồi từ {{ $adminName }}</strong>
                      </div>
                      <div class="text-body lh-lg">{!! $f->answer !!}</div>
                    </div>
                  @else
                    <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                      <i class="bi bi-hourglass-split fs-5"></i>
                      <div>
                        <strong>Đang chờ phản hồi</strong>
                        <p class="mb-0 small">Câu hỏi của bạn đang được xem xét. Chúng tôi sẽ phản hồi sớm nhất có thể.</p>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
                <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h4 class="mb-2">Không tìm thấy câu hỏi phù hợp</h4>
        <p class="text-muted mb-0">
          @if($q || ($sub ?? ''))
            Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc
          @else
            Hiện chưa có câu hỏi nào được đăng tải
          @endif
        </p>
      </div>
    @endforelse
            </div>            
            {{-- 3. Sidebar Phải (Bài viết liên quan) - Chiếm 3/12 cột --}}
            <div class="col-lg-3 d-none d-lg-block sidebar-right">
                
                {{-- Widget 3: Bạn có thể quan tâm (Bài viết/Cẩm nang) --}}
             <div class="overflow-hidden ">
            <div class="px-0 pt-2 pb-3">
                            <h3 class="heading-underline fw-bold text-uppercase m-0">
                        BÀI VIẾT NỔI BẬT
                        </h3>
                    </div>
  <div class="list-group sidebar-list">

    @forelse($topPosts as $p)
   <a href="{{ route('users.guides.show',['guide' => $p->slug]) }}"
                        class="popular-item text-decoration-none">
                        <div class="popular-thumb">
                        <img src="{{ $img($p->thumbnail) }}" alt="{{ $p->title }}">
                        </div>
                        <div class="popular-title">
                        {{ $p->title }}
                        </div>
                    </a>
    @empty
      <div class="small text-muted text-center py-3">Chưa có bài viết</div>
    @endforelse
  </div>
</div>

                {{-- Widget 4: Liên hệ nhanh --}}
             <div class="bg-light text-center p-2 shadow-sm" style="max-width:210px;;">
  <i class="bi bi-headset text-success mb-1" style="font-size:1.5rem;"></i>
  <h6 class="fw-semibold mb-1 fs-6">Bạn muốn hỏi đáp ?</h6>
  <p class="text-muted mb-2 small lh-sm">Câu hỏi của bạn sẽ<br>trả lời nhanh nhất.</p>
  <a href="{{ route('users.contact.index') }}" class="btn btn-success btn-sm w-100 py-1">Gửi câu hỏi</a>
</div>

            </div>
          </div>
    </div>
</div>
@endsection
@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush
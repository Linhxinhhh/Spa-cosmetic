@extends('Users.layouts.home')
@section('title', $guide->seo_title ?: $guide->title)
@section('meta')
    <meta name="description" content="{{ $guide->seo_description ?: Str::limit(strip_tags($guide->excerpt), 155) }}">
    <meta property="og:title" content="{{ $guide->seo_title ?: $guide->title }}">
    <meta property="og:description" content="{{ $guide->seo_description ?: Str::limit(strip_tags($guide->excerpt), 155) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="{{ $guide->thumbnail ? (Str::startsWith($guide->thumbnail,['http://','https://']) ? $guide->thumbnail : asset('storage/'.$guide->thumbnail)) : asset('images/default-post.png') }}">
@endsection

@section('content')
@php
    $tz = config('app.timezone','Asia/Ho_Chi_Minh');
    $img = function ($path) {
        if (!$path) return asset('images/default-post.png');
        return \Illuminate\Support\Str::startsWith($path, ['http://','https://'])
            ? $path : asset('storage/'.$path);
    };
    // Giả định các biến này được truyền vào từ controller
    $serviceParents = $serviceParents ?? collect([['category_name' => 'Dịch Vụ Phòng Khám', 'slug' => '#'], ['category_name' => 'Triệt Lông Diode Laser', 'slug' => '#'], ['category_name' => 'Thư Giãn & Chăm Sóc', 'slug' => '#']]);
    $hotServices = $hotServices ?? collect(); // Dịch vụ bán chạy
    $popularGuides = $popularGuides ?? collect(); // Bài viết xem nhiều
    $related = $related ?? collect(); // Bài liên quan (TẠI ĐÂY LÀ "Có thể bạn quan tâm")
@endphp
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

/* Nới khoảng bên trái item sau */
.breadcrumb-chevron .breadcrumb-item + .breadcrumb-item{
  padding-left: .70rem;
}
:root{ --accent:#ff7a00; --accent-2:#ff6a00; --muted:#6b7280; }

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
  display:flex; align-items:center; gap:6px;
  color:var(--muted); font-size:.9rem; margin-bottom:6px;
}
.stars{ color:#ffb400; font-size:.9rem; letter-spacing:1px; }
.svc-meta .dot{ opacity:.6; }

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


/* nếu danh sách xếp theo cột hẹp */
.svc-list{display:flex; flex-direction:column; gap:12px}

/* Ảnh nhỏ ở sidebar cũ (giữ nguyên nếu còn dùng) */
.thumb-svc{ width:72px; height:72px; }


/* Focus bằng bàn phím */
.svc-item:focus-visible{ outline:0; border-color:var(--accent);
  box-shadow:0 0 0 3px rgba(255,122,0,.25); }
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
  width:100%;
  aspect-ratio: 4/3;                 /* vuông 1/1 cũng được: đổi thành 1/1 nếu muốn */
  
  overflow:hidden;
  background:#f3f4f6;
}
.popular-thumb img{
  width:70%;
  height:100%;
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
</style>
<div class="bg-light min-vh-100 py-4">
    <div class="container-xl">
        
    {{-- BREADCRUMB --}}

  <nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb breadcrumb-chevron">
    <li class="breadcrumb-item">
      <a href="{{ route('users.home') }}" class="cat-link">Trang chủ</a>
    </li>
    <li class="breadcrumb-item "> <a href="{{ route('users.guides.index') }}" aria-current="page"  class="cat-link">Cẩm nang</a></li>
    <li class="breadcrumb-item active " aria-current="page">
    {{ Str::limit($guide->title, 80) }}
  </li>
  </ol>
</nav>


        <div class="row g-4">
            
            {{-- ===== LEFT SIDEBAR (col-lg-3) ===== --}}
            <aside class="col-12 col-lg-3 order-lg-1">
                <div class="d-flex flex-column gap-4">
                    

   
                  {{-- Danh mục dịch vụ --}}
                  <div class="overflow-hidden">
                        <div class="px-0 pt-2 pb-3">
                            <h3 class="heading-underline fw-bold text-uppercase m-0">
                        DỊCH VỤ
                        </h3>
                    </div>

                    <ul class="list-unstyled ps-0 d-block py-2 px-4 small text-dark text-decoration-none hover-orange ">
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
                        <span class="badge rounded-pill bg-light text-black">{{ $p->services_count }}</span>
                    @endisset
                </span>
            </a>
                        </li>
                        @empty
                        <li class="text-muted">Chưa có danh mục dịch vụ.</li>
                        @endforelse
                    </ul>
                      
                    </div>
                    {{-- Dịch vụ bán chạy --}}
                    <div class="hot-list">
                        <div class="px-0 pt-2 pb-3">
                            <h3 class="heading-underline fw-bold text-uppercase m-0">
                        DỊCH VỤ BÁN CHẠY
                        </h3>
                    </div>
                        @forelse($hotServices as $s)
                            @php
                            $name   = $s->name ?? $s->service_name;
                            $thumb  = $img($s->thumbnail ?? null);
                            
                            
                            $rating = $s->rating_avg ?? 4.8;           // fallback
                            $count  = $s->rating_count ?? 12;          // fallback
                            $time   = $s->duration_text ?? '5 phút';   // fallback
                            $pct    = $s->sold_percent ?? 33;          // % bán / khuyến nghị (fallback)
                            @endphp
                          @php
    $price         = $s->price ?? 0;           
    $priceSale     = $s->price_sale ?? 0;      
    $priceOriginal = $s->price_original ?? $price;
@endphp
@php
    $price = number_format((float)($s->price ?? 0), 0, ',', '.');
@endphp
@if($priceSale > 0)
    {{-- GIÁ SALE --}}
    <div class="fw-bold text-danger">
        {{ number_format($priceSale, 0, ',', '.') }} đ
    </div>

    {{-- GIÁ GỐC --}}
    <div class="text-muted text-decoration-line-through small">
        {{ number_format($priceOriginal, 0, ',', '.') }} đ
    </div>
@else

@endif

                     
                               
                    

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
            </aside>

            {{-- ===== CENTER / MAIN CONTENT (col-lg-6) ===== --}}
            <main class="col-12 col-lg-6 order-lg-2">
                <div class="bg-white p-4 p-md-5 rounded shadow-sm">
                    
                    <h1 class="h3 fw-bolder text-dark">{{ $guide->title }}</h1>

  {{-- META INFO (đã null-safe) --}}
<div class="mt-3 d-flex flex-wrap align-items-center gap-3 small text-muted border-bottom pb-3">
  <span>{{ $guide->published_at?->copy()->tz($tz)->format('d/m/Y H:i') ?? '-' }}</span>
  <span>•</span>
  <span>Chuyên mục: <strong class="text-dark">{{ $guide->category->name ?? '—' }}</strong></span>
  <span>•</span>
  <span><i class="fa-regular fa-eye me-1"></i> {{ number_format($guide->views) }} lượt xem</span>
</div>

                 {{-- TAGS (đổi route) --}}
@if($guide->tags->count())
  <div class="mt-3 d-flex flex-wrap gap-2">
    @foreach($guide->tags as $t)
      <a href="{{ route('users.guides.index', ['tag' => $t->tag_id]) }}"
         class="badge text-decoration-none bg-primary-100 text-primary-700 fw-normal hover-bg-primary-200">
        #{{ $t->name }}
      </a>
    @endforeach
  </div>
@endif

                  {{-- THUMBNAIL --}}
@if($guide->thumbnail)
  <img class="mt-4 w-100 rounded shadow-sm"
       src="{{ Str::startsWith($guide->thumbnail,['http://','https://']) ? $guide->thumbnail : asset('storage/'.$guide->thumbnail) }}"
       alt="{{ $guide->title }}">
@endif

                    {{-- Article Content --}}
                    <article class="article-content mt-4">
                        {!! $guide->content_html !!}
                    </article>

                   {{-- BÀI LIÊN QUAN (CENTER, đổi route + null-safe datetime) --}}
@if($related->count())
  <div class="mt-5 pt-4 border-top">
    <h2 class="fw-bold h5 mb-4">Bài liên quan</h2>
    <div class="row row-cols-1 row-cols-sm-2 g-3">
      @foreach($related as $r)
        <div class="col">
          <a href="{{ route('users.guides.show', ['guide' => $r->slug]) }}"
             class="d-flex gap-3 text-decoration-none hover-orange">
            <img class="w-72px h-50px object-cover rounded bg-light"
                 src="{{ $r->thumbnail ? $img($r->thumbnail) : asset('images/default-post.png') }}"
                 alt="{{ $r->title }}">
            <div>
              <div class="small text-muted">
                {{ $r->published_at?->copy()->tz($tz)->format('d/m/Y H:i') ?? '-' }}
              </div>
              <div class="fw-semibold text-dark text-truncate-2 transition-color">
                {{ $r->title }}
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  </div>
@endif
                </div>
            </main>

            {{-- ===== RIGHT SIDEBAR (col-lg-3) ===== --}}
            <aside class="col-12 col-lg-3 order-lg-3">
                <div class="   overflow-hidden sticky-top" style="top: 1.5rem;">
                <div class="px-0 pt-2 pb-3">
                    <h3 class="heading-underline fw-bold text-uppercase m-0">
                BÀI VIẾT XEM NHIỀU
                </h3>
                </div>

                <div class="popular-list">
                    @forelse($popularGuides as $p)
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
                    <div class="small text-muted text-center py-3">Chưa có dữ liệu</div>
                    @endforelse
                </div>
                </div>

            </aside>

        </div>
    </div>
</div>


<style>
    /* tiêu đề chung có các mục dịch vụ, dịch vụ bán chạy, hỏi dap, bài viết xem nhiều*/
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
    /* Global classes and utility mappings */
    .img-cover{width:100%;height:100%;object-fit:cover;object-position:center}
    .transition-scale:hover{transform: scale(1.1);}
    .transition-color:hover{color: #f97316 !important;} /* Orange-600 */
    .hover-orange:hover{color: #f97316 !important;}
    .hover-bg-light-orange:hover{background-color: #ffedd5 !important;} /* Orange-100 */
    .hover-bg-light:hover{background-color: #f8f9fa !important;}
    .bg-gradient-orange{background: linear-gradient(to right, #f97316 0%, #ea580c 100%);}
    .small-text{font-size: 0.6875rem;} /* ~11px */
    .text-truncate-2{
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .letter-spacing-wide{letter-spacing: 0.05em;}
    .bg-white-15{background-color: rgba(255,255,255,0.15);}
    .py-2\.5{padding-top: 0.625rem !important; padding-bottom: 0.625rem !important;}
    .hover-shadow-lg:hover{box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;}
    .h6{font-size: 1rem !important;} /* Fix for card title font size */


    /* Layout cho danh sách bài viết dưới hỏi đáp */
    .thumb-svc{width:72px;height:72px}
    .thumb-pop{width:96px;height:64px}
    .hero-main{height:320px}
    .hero-side{height:155px}
    .card-16x9{position:relative;width:100%;aspect-ratio:16/9}

    /* phần banner chính */
    .overlay-hero{
        position:absolute;inset:0;
        background:linear-gradient(to top, rgba(0,0,0,.55) 0%, rgba(0,0,0,.15) 45%, rgba(0,0,0,0) 100%);
    }
    .overlay-card{
        position:absolute;inset:0;
        background:linear-gradient(to top, rgba(0,0,0,.65) 0%, rgba(0,0,0,.2) 50%, rgba(0,0,0,0) 100%);
    }

    /* Hero phần chính bài viết */
    .hero-title{
        position:absolute;left:0;right:0;bottom:0;
        padding:14px;color:#fff;font-weight:700;
        text-shadow:0 1px 2px rgba(0,0,0,.35);
    }
    .side-title{
        position:absolute;left:0;right:0;bottom:0;
        padding:10px;color:#fff;font-weight:600;
        font-size:14px;line-height:1.3;
        text-shadow:0 1px 2px rgba(0,0,0,.35);
    }
    .bg-secondary-dark{background-color: #3f3f46;} /* Dùng màu xám đậm tương tự Slate-700 */
    .bg-primary-100{background-color: #dbeafe !important;}
    .text-primary-700{color: #1d4ed8 !important;}
    .hover-bg-primary-200:hover{background-color: #bfdbfe !important;}
    .w-72px{width: 72px;}
    .h-50px{height: 50px;}
    
    .text-truncate-2{
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .letter-spacing-wide{letter-spacing: 0.05em;}

    /* Layout size helpers */
    .thumb-svc{width:72px;height:72px}
    .thumb-pop{width:96px;height:64px}
    
    /* Ensure the article content is well-formatted if using a rich text editor */
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
</style>
@endsection
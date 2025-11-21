@extends('Users.servicehome')
@section('title','Cẩm nang')

@section('content')
{{-- Hàm ảnh, giờ, dịch vụ, giá --}}
    @php
        $tz = config('app.timezone','Asia/Ho_Chi_Minh');
        $img = function ($path) {
            if (!$path) return asset('images/default-post.png');
            return \Illuminate\Support\Str::startsWith($path, ['http://','https://'])
                ? $path : asset('storage/'.$path);
        };
        $serviceCats   = $serviceCats   ?? collect();
        $hotServices   = $hotServices   ?? collect();
        $popularGuides = $popularGuides ?? collect();
        $hero          = $hero ?? null;
        $heroSides     = $heroSides ?? collect();
        $categories    = $categories ?? collect();
        $tags          = $tags ?? collect();
        $q             = $q ?? '';
        $category      = $category ?? null;
        $tag           = $tag ?? null;
        $guides        = $guides ?? collect();
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

/
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
</style>

<div class="bg-light min-vh-100 py-4">
    <div class="container-xl">
        
        {{--  (Thanh điều hướng) --}}
        <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-chevron">
            <li class="breadcrumb-item">
            <a href="{{ route('users.home') }}" class="cat-link">Trang chủ</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Cẩm nang</li>
        </ol>
        </nav>


        <div class="row g-4"> {{-- Thay thế grid grid-cols-12 gap-6 bằng row g-4 --}}

            {{-- ===== LEFT SIDEBAR (col-lg-3) ===== --}}
            <aside class="col-12 col-lg-3 order-lg-1">
                <div class="d-flex flex-column gap-4">
                    
              

                    {{-- Danh mục dịch vụ --}}
                                    
                  <div class=" overflow-hidden">
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
            </aside>

            {{-- ===== CENTER / MAIN CONTENT (col-lg-6) ===== --}}
            <main class="col-12 col-lg-6 order-lg-2">
                <div class="d-flex flex-column gap-4">

                    {{-- Top banners (Hero) --}}
                    <div class="row g-3">
                        {{-- Banner chính --}}
                        <div class="col-12 col-md-8">
                            <a class="d-block  overflow-hidden shadow hero-main position-relative group-hover"
                             href="{{ $hero ? route('users.guides.show', ['guide' => $hero->slug]) : '#' }}">
                                <img class="img-cover transition-scale"
                                     src="{{ $hero ? $img($hero->thumbnail) : asset('images/default-post.png') }}"
                                     alt="{{ $hero->title ?? 'banner' }}">
                                <div class="overlay-hero"></div>
                                @if($hero)
                                    <h3 class="hero-title">{{ $hero->title }}</h3>
                                @endif
                            </a>
                        </div>
                        
                        {{-- 2 banner nhỏ --}}
                        <div class="col-md-4 d-none d-md-flex flex-column gap-3">
                            @foreach($heroSides->take(2) as $h)
                                <a class="d-block overflow-hidden shadow hero-side position-relative group-hover"
                                   href="{{ route('users.guides.show', ['guide' => $h->slug]) }}">
                                    <img class="img-cover transition-scale" 
                                         src="{{ $img($h->thumbnail) }}" 
                                         alt="{{ $h->title }}">
                                    <div class="overlay-hero"></div>
                                    <div class="side-title">{{ $h->title }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                

                    {{-- Tiêu đề HỎI ĐÁP --}}
                    <div class="row g-3">
                          <div class="px-0 pt-2 pb-3">
                            <h3 class="heading-underline fw-bold text-uppercase m-0">
                        HỎI ĐÁP
                        </h3>
                    </div>
                        @foreach($items as $g)
                            <div class="col-12 col-md-6 col-lg-4">
                            <a href="{{ route('users.faq.index', $g->slug) }}" class="card h-100 border-0 shadow-sm text-decoration-none">
                                <div class="ratio ratio-16x9">
                                <img src="{{ $g->cover_image ? asset('storage/'.$g->cover_image) : asset('images/placeholder-16x9.jpg') }}"
                                    class="w-100 h-100 object-fit-cover" alt="{{ $g->question  }}">
                                </div>
                                <div class="card-body">
                                <h3 class="h6 text-truncate-2 mb-0">{{ $g->question }}</h3>
                                </div>
                            </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                    {{ $items->links() }}
                    </div>
                    

                    {{-- Danh sách bài viết (Guides/Q&A) --}}
                    @if($guides->count())
                        <div class="row row-cols-1 row-cols-sm-2 g-4">
                            @foreach($guides as $g)
                                <div class="col">
                                    <a href="{{ route('users.guides.show', ['guide' => $g->slug]) }}"
                                       class="d-block  overflow-hidden bg-white shadow-sm hover-shadow-lg position-relative card-16x9 group-hover">
                                        <img class="img-cover transition-scale"
                                             src="{{ $img($g->thumbnail) }}" 
                                             alt="{{ $g->title }}">
                                        <div class="overlay-card"></div>
                                        <div class="position-absolute bottom-0 start-0 end-0 p-3">
                                            <div class="d-flex align-items-center gap-2 text-white-50 small-text">
                                                <span>{{ optional($g->published_at)->tz($tz)->format('d/m/Y H:i') }}</span>
                                                <span>•</span>
                                                <span class="px-1 py-0 rounded bg-white-15">{{ $g->category->name ?? '—' }}</span>
                                            </div>
                                            <h3 class="mt-2 text-white fw-semibold line-clamp-2 h6">
                                                {{ $g->title }}
                                            </h3>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 d-flex justify-content-center">
                            {{-- Sử dụng Blade pagination cho Bootstrap --}}
                            {{ $guides->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="bg-white rounded shadow-sm p-5 text-center">
                            <div class="text-secondary mb-3">
                                <i class="fa-solid fa-file-circle-question fa-4x"></i>
                            </div>
                            <p class="text-muted">Chưa có bài viết nào</p>
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

                <div class="">
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
</style>
@endsection
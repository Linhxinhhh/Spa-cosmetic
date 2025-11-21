@extends('Users.servicehome')
@section('title','Dịch vụ')

@push('styles')
<style>

  .svc-card{border:1px solid #e9ecef;border-radius:14px;overflow:hidden;background:#fff;}
  .svc-head{padding:12px 12px 0;color:#6c757d;font-size:.9rem;font-weight:600;display: -webkit-box;
    -webkit-line-clamp: 3; 
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
    max-height: calc(1.3em * 2);}

.svc-thumb-wrapper {
    width: 100%;
    height: 200px;       /* CHÍNH size ảnh cố định */
    overflow: hidden;
    border-radius: 10px;
    background: #f2f2f2; /* nền xám nhạt khi ảnh load */
}

.svc-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;   /* quan trọng nhất */
    object-position: center; /* giữ tâm ảnh */
    display: block;
}

.svc-title {
  
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 44px;  /* Giữ cố định chiều cao tên */
}

/* Mô tả ngắn – giới hạn 3 dòng */
.svc-desc {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    color:#667085; 
    min-height: 60px; /* Cố định chiều cao mô tả */
}
  .svc-body{padding:12px;}
  .svc-price{color:#ff6a00;font-weight:800;font-size:1.1rem}
  .svc-compare{color:#98a2b3;text-decoration:line-through;margin-left:.35rem}
  .svc-meta{color:#6c757d;font-size:.9rem}
  
  .svc-actions .btn-call{background:#0b8a2a;border:none}
  .svc-actions .btn-book{background:#ff7a00;border:none}
  .svc-actions .btn{border-radius:.6rem}
  .discount-badge{position:absolute;right:8px;bottom:8px}
  .icon{width:18px;height:18px;vertical-align:-3px;margin-right:4px;opacity:.85}
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
  .popular-list{
  list-style: none;   /* bỏ bullet */
  padding-left: 0;    /* bỏ thụt đầu dòng mặc định */
  margin: 0;
}
.popular-item{
  list-style: none;   /* phòng khi browser vẫn render */
}
  .popular-thumb{
    display:block;
    width:100%;
    aspect-ratio:4/3;      /* hoặc 1/1 nếu muốn */
    overflow:hidden;
    background:#f3f4f6;

  }
  .popular-thumb img{
    width:100%;height:100%;
    object-fit:cover;
    transition:transform .2s ease;
  }
  .popular-thumb:hover img{ transform:scale(1.04); }

  .popular-title{
    display:block;
    margin-top:.5rem;
    font-size:14px;
    line-height:1.35;
    color:#111827;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
  }
  aside.sticky-top {
    max-height: 100vh; /* Giới hạn chiều cao để tránh overflow khi sticky */
    overflow-y: auto; /* Scroll nội dung sidebar nếu quá dài */
}
</style>
@endpush

@section('content')
<div class="container py-4">

  {{-- Banner --}}
  <div class="mb-4">
    <img class="w-100 rounded shadow-sm" src="{{ asset('images/logos/image.png') }}" alt="Clinic & Spa">
  </div>


  <div class="row g-4">
   {{-- Sidebar: bộ lọc --}}
<aside class="col-lg-3 sticky-top" style="top: 1rem; z-index: 1020;">
  
  <div class="card shadow-sm">
    
    <div class="card-body">
      
      <h3 class="heading-underline fw-bold text-uppercase m-0">
        LYN CLINIC & SPA
      </h3>

      <ul class="list-unstyled mb-0 svc-cats ps-3 text-black">
        @forelse($serviceParents as $p)
          @php
            $slug = $p->slug ?? null;
            $name = $p->category_name ?? 'Danh mục';
            $to   = !empty($slug)
                    ? route('users.services.byCategory', ['category' => $slug])   // {category:slug}
                    : route('users.services.index', ['category_id' => $p->category_id]); // fallback
          @endphp

          <li class="mb-2">
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
          <li class="text-muted">Chưa có danh mục dịch vụ.</li>
        @endforelse
      </ul>

      <h5 class="mb-3">Bộ lọc</h5>
      <form class="bottom:20px;" method="GET" action="{{ route('users.services.index') }}">
        <div class="mb-3">
          <label class="form-label">Từ khóa</label>
          <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Tìm dịch vụ...">
        </div>

        <div class="mb-3">
          <label class="form-label">Danh mục</label>
          <select name="category_id" class="form-select">
            <option value="">-- Tất cả --</option>
            @foreach($categories as $c)
              <option value="{{ $c->category_id }}" @selected(($catId ?? null) == $c->category_id)>
                {{ $c->category_name ?? $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Khoảng giá (đ)</label>
          <div class="d-flex gap-2">
            <input type="number" class="form-control" name="min_price" placeholder="Từ" value="{{ $min ?: '' }}" step="10000" min="0">
            <input type="number" class="form-control" name="max_price" placeholder="Đến" value="{{ $max ?: '' }}" step="10000" min="0">
          </div>
        </div>

        <button class="btn btn-success w-100">Áp dụng</button>
      </form>
      

      {{-- CẨM NANG: phổ biến --}}
      @if(isset($popularGuides) && $popularGuides->count())
        <h3 class="heading-underline fw-bold text-uppercase m-0">
          BÀI VIẾT XEM NHIỀU
        </h3>
        <ul class="popular-list">
          @foreach($popularGuides as $g)
            @php $url = route('users.guides.show', $g->slug); @endphp
            <li class="popular-item mb-3">
              <a href="{{ $url }}" class="popular-thumb shadow-sm">
                <img src="{{ $g->thumb_url }}" alt="{{ $g->title }}">
              </a>
              <a href="{{ $url }}" class="popular-title text-decoration-none">
                {{ $g->title }}
              </a>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</aside>

    {{-- Main --}}
    <section class="col-lg-9">
      {{-- Tabs sort + Hiển thị --}}
      <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <ul class="nav nav-pills gap-2">
          @php
            $sort = request('sort', 'new');
            $tab = fn($k,$t)=>'<li class="nav-item"><a class="nav-link '.($sort==$k?'active':'').'" href="'.route('users.services.index', request()->merge(['sort'=>$k])->query()).'">'.$t.'</a></li>';
          @endphp
          {!! $tab('new','Mới nhất') !!}
          {!! $tab('popular','Bán chạy') !!}
          {!! $tab('price_asc','Giá thấp đến cao') !!}
          {!! $tab('price_desc','Giá cao đến thấp') !!}
        </ul>

        <form>
          @foreach(request()->except('per_page') as $k=>$v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
          @endforeach
          <select name="per_page" class="form-select" onchange="this.form.submit()">
            @foreach([12,24,42,60] as $n)
              <option value="{{ $n }}" @selected($perPage==$n)>Hiển thị: {{ $n }}</option>
            @endforeach
          </select>
        </form>
      </div>

      {{-- Grid --}}
      <div class="row g-3">
        @forelse($services as $sv)
          <div class="col-md-6 col-xl-4">
            <div class="svc-card h-100">
              <div class="svc-head">
                {{ $sv->category->category_name ?? $sv->category->name ?? 'Dịch vụ' }}
              </div>

              @php
                $thumb = $sv->thumbnail ? Storage::url($sv->thumbnail) : asset('images/placeholder-4x3.jpg');
                $p   = (float) ($sv->price ?? 0);
                $po  = (float) ($sv->price_original ?? 0);
                $ps  = (float) ($sv->price_sale ?? 0);
                $display = $p > 0 ? $p : ($ps > 0 ? $ps : $po);
                $compare = 0;
                if ($display == $ps && $po > $ps)       $compare = $po;
                elseif ($display == $p && $po > $p)     $compare = $po;
                $hasCompare = $compare > $display && $display > 0;
                $discount   = $hasCompare ? (100 - round($display * 100 / $compare)) : null;
              @endphp

<a href="{{ route('users.services.show', $sv->slug) }}" class="position-relative d-block svc-thumb-container">
    <img class="svc-thumb" 
         src="{{ $thumb }}" 
         alt="{{ $sv->service_name }}"
         style="width: 100%; height: 200px; object-fit: cover; object-position: center;">
    
    @if($hasCompare)
        <span class="badge bg-success position-absolute top-0 start-0 m-2 z-1">-{{ $discount }}%</span>
    @endif
</a>

              <div class="svc-body d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <div class="svc-meta">
                    @if(!empty($sv->booked_count))
                      <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-5 0-9 2.5-9 5v1h18v-1c0-2.5-4-5-9-5Z"/></svg>
                      {{ $sv->booked_count }} người mua
                    @endif
                  </div>
                  <div>
                    <span class="svc-price">{{ number_format($display,0,',','.') }} đ</span>
                    @if($hasCompare)
                      <span class="svc-compare">{{ number_format($compare,0,',','.') }} đ</span>
                    @endif
                  </div>
                </div>

                <h6 class="mt-1 mb-2">
                  <a class=" svc-title text-decoration-none text-dark" href="{{ route('users.services.show',$sv->slug) }}">{{ $sv->service_name }}</a>
                </h6>
<div class="svc-meta mb-2">
    @if($sv->type == 'Gói')
        Trọn gói
    @elseif($sv->type == 'Lẻ')
        1 lần
    @elseif($sv->type == 'trial')
        Trải nghiệm
    @else
        1 lần
    @endif

    <span class="mx-2">|</span> 
    {{ $sv->duration ?? 0 }} phút
</div>


                <p class="svc-desc mb-3">{{ \Illuminate\Support\Str::limit($sv->short_desc ?? strip_tags($sv->description), 110) }}</p>

              <div class="d-flex gap-2 svc-actions mt-auto">
  <a href="tel:18006778" class="text-white btn btn-sm btn-call flex-grow-1"> <i class="bi bi-telephone-fill" aria-hidden="true"></i>
    18006778<br> <small>(nhấn phím 2)</small>
  </a>
  <a href="{{ route('users.booking.create', ['service' => $sv->slug]) }}" class="btn btn-sm btn-book text-white text">
    Đặt hẹn
  </a>
</div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="alert alert-info">Chưa có dịch vụ phù hợp bộ lọc.</div>
          </div>
        @endforelse
      </div>

      <div class="mt-4">
        {{ $services->links() }}
      </div>
    </section>
  </div>
</div>
@endsection

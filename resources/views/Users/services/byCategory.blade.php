@extends('Users.servicehome')
@section('title', 'Dịch vụ: '.$category->category_name)

@push('styles')
<style>
  .svc-card{border:1px solid #e9ecef;border-radius:14px;overflow:hidden;background:#fff;}
  .svc-thumb{width:100%;height:220px;object-fit:cover;display:block;}
  .svc-body{padding:12px;}
  .svc-price{color:#ff6a00;font-weight:800;font-size:1.1rem}
  .svc-compare{color:#98a2b3;text-decoration:line-through;margin-left:.35rem}
  .discount-badge{position:absolute;right:8px;bottom:8px}
</style>
@endpush

@section('content')
<div class="container py-4">

  <div class="mb-4">
    <h3 class="mb-1">Danh mục: {{ $category->category_name }}</h3>
    @if($children->count())
      <div class="small text-muted">
        Nhánh con:
        @foreach($children as $c)
          <a class="me-2" href="{{ route('users.services.byCategory', $c->slug) }}">{{ $c->category_name }}</a>
        @endforeach
      </div>
    @endif
  </div>

  <div class="row g-4">
    {{-- Sidebar: danh mục cha --}}
    <aside class="col-lg-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="mb-3">Danh mục dịch vụ</h5>
          <ul class="list-unstyled mb-4">
            @forelse($serviceParents as $p)
              <li class="mb-2">
                <a class="text-dark {{ $p->category_id === $category->category_id ? 'fw-semibold' : '' }}"
                   href="{{ route('users.services.byCategory', $p->slug) }}">
                  {{ $p->category_name }}
                </a>
              </li>
            @empty
              <li class="text-muted">Chưa có danh mục cha.</li>
            @endforelse
          </ul>

          {{-- Bộ lọc nhanh (tùy chọn) --}}
          <form method="GET" class="border-top pt-3">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <div class="mb-3">
              <label class="form-label">Từ khóa</label>
              <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="Tìm dịch vụ...">
            </div>
            <div class="mb-3">
              <label class="form-label">Khoảng giá</label>
              <div class="d-flex gap-2">
                <input type="number" class="form-control" name="min_price" placeholder="Min" value="{{ $min ?: '' }}">
                <input type="number" class="form-control" name="max_price" placeholder="Max" value="{{ $max ?: '' }}">
              </div>
            </div>
            <button class="btn btn-primary w-100">Lọc</button>
          </form>
        </div>
      </div>
    </aside>

    {{-- Main list --}}
    <section class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted">Tìm thấy {{ $services->total() }} dịch vụ</div>
        <form>
          @foreach(request()->except('sort') as $k=>$v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
          @endforeach
          <select name="sort" class="form-select" style="max-width:220px" onchange="this.form.submit()">
            <option value="new"        @selected($sort==='new')>Mới nhất</option>
            <option value="popular"    @selected($sort==='popular')>Bán chạy</option>
            <option value="price_asc"  @selected($sort==='price_asc')>Giá tăng dần</option>
            <option value="price_desc" @selected($sort==='price_desc')>Giá giảm dần</option>
          </select>
        </form>
      </div>

      <div class="row g-3">
        @forelse($services as $sv)
          @php
            $p   = (float) ($sv->price ?? 0);
            $po  = (float) ($sv->price_original ?? 0);
            $ps  = (float) ($sv->price_sale ?? 0);
            $display = $ps>0 ? $ps : ($p>0 ? $p : $po);
            $compare = ($po > $display) ? $po : 0;
            $discount = $compare ? (100 - round($display*100/$compare)) : null;

            $thumb = $sv->thumbnail
              ? (Str::startsWith($sv->thumbnail, ['http://','https://'])
                    ? $sv->thumbnail
                    : (\Storage::exists($sv->thumbnail) ? \Storage::url($sv->thumbnail) : asset('uploads/'.$sv->thumbnail)))
              : asset('images/placeholder-4x3.jpg');
          @endphp
          <div class="col-md-6 col-xl-4">
            <div class="svc-card h-100">
              <a href="{{ route('users.services.show', $sv->slug) }}" class="position-relative d-block">
                <img class="svc-thumb" src="{{ $thumb }}" alt="{{ $sv->service_name }}">
                @if($discount)
                  <span class="badge bg-success discount-badge">-{{ $discount }}%</span>
                @endif
              </a>
              <div class="svc-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div class="small text-muted">{{ $sv->category->category_name ?? 'Dịch vụ' }}</div>
                  <div>
                    <span class="svc-price">{{ number_format($display,0,',','.') }} đ</span>
                    @if($compare)
                      <span class="svc-compare">{{ number_format($compare,0,',','.') }} đ</span>
                    @endif
                  </div>
                </div>
                <h6 class="mt-1 mb-2">
                  <a class="text-decoration-none text-dark" href="{{ route('users.services.show', $sv->slug) }}">
                    {{ $sv->service_name }}
                  </a>
                </h6>
                <div class="text-muted small mb-2">1 lần · {{ $sv->duration ?? 0 }} phút</div>
                <p class="text-muted small mb-3">{{ \Illuminate\Support\Str::limit($sv->short_desc ?? strip_tags($sv->description), 110) }}</p>
                <div class="d-flex gap-2 mt-auto">
                  <a href="{{ route('users.booking.create', ['service' => $sv->slug]) }}" class="btn btn-warning">Đặt hẹn</a>
                  <a href="{{ route('users.services.show', $sv->slug) }}" class="btn btn-outline-secondary">Xem</a>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="alert alert-info">Chưa có dịch vụ trong danh mục này.</div>
          </div>
        @endforelse
      </div>

      <div class="mt-4">{{ $services->links() }}</div>
    </section>
  </div>
</div>
@endsection

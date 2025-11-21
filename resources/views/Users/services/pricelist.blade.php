@extends('Users.servicehome')

@section('content')
<div class="container py-4">
@push('styles')
<style>
  .best-sell-title { font-weight: 800; font-size: 1.1rem; }
  .best-sell-accent { width: 60px; height: 3px; background:#ff6a00; margin:6px 0 14px; }
  .best-item { margin-bottom: 24px; }
  .best-item-img { width:100%; height:140px; object-fit:cover; border-radius:6px; }
  .best-item-price { color:#ff6a00; font-weight:800; font-size:1.25rem; margin-top:10px; }
  .best-item-name { margin:4px 0 6px; font-weight:600; line-height:1.2; }
  .best-item-meta { font-size:.875rem; color:#6c757d; }
  .star { color:#ff6a00; }

  /* =======================
        FIX PAGINATION
     ======================= */
  .pagination {
      display: flex !important;
      justify-content: center !important;
      align-items: center !important;
      flex-wrap: nowrap !important;
      gap: 4px !important;
  }

  .pagination .page-item {
      display: inline-block !important;
  }

  .pagination .page-link {
      display: inline-block !important;
      width: auto !important;
      height: auto !important;
      padding: 6px 14px !important;
      margin: 0 !important;
      border-radius: 6px !important;
  }

  .pagination .page-item.active .page-link {
      background-color: #ff6a00 !important;
      border-color: #ff6a00 !important;
      color: #fff !important;
  }

  .pagination .page-item.disabled .page-link {
      color: #6c757d !important;
      cursor: not-allowed !important;
  }
</style>
@endpush

  <div class="row"> {{-- PHẢI có .row để grid hoạt động --}}

   {{-- SIDEBAR TRÁI --}}
@if($topServices->isNotEmpty())
  <aside class="col-12 col-lg-3 mb-4">
    <div class="best-sell-title text-uppercase">Dịch vụ bán chạy</div>
    <div class="best-sell-accent"></div>

    @foreach($topServices as $ts)
      <div class="best-item">
        <a href="{{ route('users.services.show', $ts->slug) }}" class="text-decoration-none text-body">
         @if($ts->thumbnail)
          <img class="best-item-img"
              src="{{ asset('storage/'.$ts->thumbnail) }}"
              alt="{{ $ts->service_name }}">
        @endif

          <div class="best-item-price">
            {{ number_format($ts->effective_price, 0, ',', '.') }} đ
          </div>

          <div class="best-item-name">
            {{ $ts->service_name }}
          </div>

          <div class="best-item-meta">
            {{-- Hàng sao kiểu minh hoạ – bạn có thể thay rating thật nếu có --}}
            <span class="star">★★★★★</span>
            <span> ({{ $ts->booked_count }}) </span>
            <span class="mx-2">|</span>
            {{-- info gói & thời lượng (tuỳ bạn có trường nào) --}}
            @if(!empty($ts->duration))
              <span>{{ $ts->duration }} phút</span>
            @else
              <span>1 lần</span>
            @endif
          </div>
        </a>
      </div>
    @endforeach
  </aside>
@endif

    {{-- MAIN PHẢI --}}
    <main class="col-12 col-lg-9">
      <h1 class="mb-3">Bảng giá dịch vụ</h1>

      <form class="card card-body mb-4" method="GET" action="{{ route('users.pricelist.index') }}">
        <div class="row g-2">
          <div class="col-md-4">
            <input type="text" name="q" class="form-control" placeholder="Tìm dịch vụ..." value="{{ $kw ?? '' }}">
          </div>
          <div class="col-md-3">
                 <select name="category_id" class="form-select">
          <option value="">-- Danh mục --</option>
          @foreach($categories as $c)
            <option value="{{ $c->category_id }}" @selected(($catId ?? null) == $c->category_id)>
              {{ $c->category_name ?? $c->name }}
            </option>
          @endforeach
        </select>
          </div>
          <div class="col-md-3">
           <div class="d-flex gap-2">
              <input type="number" name="min_price" class="form-control"
                    placeholder="Giá từ" value="{{ $min ?? '' }}" step="10000" min="0">
              <input type="number" name="max_price" class="form-control"
                    placeholder="Đến" value="{{ $max ?? '' }}" step="10000" min="0">
            </div>
          </div>
          <div class="col-md-2">
            <select name="sort" class="form-select">
              <option value="price_asc"  @selected(($sort ?? '')==='price_asc')>Giá tăng dần</option>
              <option value="price_desc" @selected(($sort ?? '')==='price_desc')>Giá giảm dần</option>
              <option value="newest"     @selected(($sort ?? '')==='newest')>Mới nhất</option>
            </select>
          </div>
        </div>
        <div class="mt-3">
          <button class="btn btn-primary">Lọc</button>
          <a href="{{ route('users.pricelist.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
        </div>
      </form>

      {{-- Bảng giữ nguyên --}}
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Dịch vụ</th>
              <th>Danh mục</th>
              <th>Thời lượng</th>
              <th class="text-end">Giá</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($services as $s)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    @if($s->image)
                      <img src="{{ asset('storage/'.$s->image) }}" alt="" width="64" height="64" class="rounded">
                    @endif
                    <div>
                      <div class="fw-semibold">{{ $s->service_name }}</div>
                      <div class="text-muted small">{{ Str::limit($s->description, 90) }}</div>
                    </div>
                  </div>
                </td>
                <td>{{ optional($s->category)->category_name }}</td>
                <td>{{ $s->duration ? $s->duration.' phút' : '-' }}</td>
                <td class="text-end">
                  @php $ep = $s->effective_price; @endphp
                  @if(!is_null($s->price_sale) && $s->price_sale > 0 && (is_null($s->price) || $s->price == 0))
                    <div><span class="fw-bold">{{ number_format($ep, 0, ',', '.') }} đ</span></div>
                    @if($s->price_original && $s->price_original > $ep)
                      <div class="text-muted text-decoration-line-through small">
                        {{ number_format($s->price_original, 0, ',', '.') }} đ
                      </div>
                    @endif
                  @else
                    <div class="fw-bold">{{ number_format($ep, 0, ',', '.') }} đ</div>
                  @endif
                </td>
                <td class="text-end">
                  <a href="{{ route('users.services.show', $s->slug) }}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted">Không có dịch vụ phù hợp.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Phân trang tùy chỉnh hoàn toàn để tránh lỗi --}}
@if($services->hasPages())
<nav aria-label="Phân trang dịch vụ" class="mt-4">
    <ul class="pagination">

        {{-- Nút Trước --}}
        <li class="page-item {{ $services->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $services->previousPageUrl() }}">Trước</a>
        </li>

        {{-- Trang số --}}
        @for ($i = 1; $i <= $services->lastPage(); $i++)
            <li class="page-item {{ $i == $services->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $services->url($i) }}">{{ $i }}</a>
            </li>
        @endfor

        {{-- Nút Sau --}}
        <li class="page-item {{ !$services->hasMorePages() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $services->nextPageUrl() }}">Sau</a>
        </li>

    </ul>

    <div class="text-center text-muted small mt-2">
        Hiển thị {{ $services->firstItem() }} đến {{ $services->lastItem() }} của {{ $services->total() }} kết quả
    </div>
</nav>
@endif


    </main>

  </div> {{-- /row --}}
</div>
@endsection
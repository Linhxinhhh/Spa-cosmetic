@extends('Users.layouts.home')

@section('title', 'Thương hiệu: ' . ($brand->brand_name ?? ''))

@section('content')
<div class="container py-5">

  {{-- Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">
        <i class="fas fa-tags me-2 text-secondary"></i>
        Thương hiệu: {{ $brand->brand_name }}
      </h1>
      <div class="text-muted">Tổng sản phẩm: {{ $products->total() }}</div>
    </div>

    {{-- Sắp xếp --}}
    <form method="GET"
          action="{{ route('users.products.byBrand', ['brand' => $brand->slug]) }}"
          class="d-flex align-items-center gap-2">
      @foreach(request()->except(['sort','page']) as $k => $v)
        @if(is_array($v))
          @foreach($v as $vv)
            <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
          @endforeach
        @else
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
      @endforeach

      <label class="text-muted small mb-0">Sắp xếp</label>
      <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">Mặc định</option>
        <option value="newest"     {{ request('sort')==='newest'?'selected':'' }}>Mới nhất</option>
        <option value="price_asc"  {{ request('sort')==='price_asc'?'selected':'' }}>Giá: Thấp → Cao</option>
        <option value="price_desc" {{ request('sort')==='price_desc'?'selected':'' }}>Giá: Cao → Thấp</option>
      </select>
    </form>
  </div> {{-- đóng header --}}

  {{-- Grid sản phẩm --}}
  <div class="row g-4">
    @forelse($products as $product)
      @include('Users.partials.product-card', ['product' => $product])
    @empty
      <div class="col-12 text-center text-muted py-5">Chưa có sản phẩm.</div>
    @endforelse
  </div>

  {{-- Phân trang (nếu có) --}}
  {{ $products->withQueryString()->links() }}

</div>
@endsection

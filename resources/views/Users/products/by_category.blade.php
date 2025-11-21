@extends('Users.layouts.home')

@section('title', 'Danh mục: ' . ($category->category_name ?? ''))

@section('content')
{{-- Breadcrumb --}}
<div class="d-flex justify-content-between align-items-center mb-3" style="margin-left:120px;margin-top:30px;">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      {{-- Trang chủ --}}
      <li class="breadcrumb-item">
        <a href="{{ route('users.home') }}">Trang chủ</a>
      </li>

      {{-- Danh mục cha (nếu có) --}}
      @if($category->parent)
        <li class="breadcrumb-item">
          <a href="{{ route('users.products.byCategory', $category->parent->slug ?? $category->parent->category_id) }}">
            {{ $category->parent->category_name }}
          </a>
        </li>
      @endif
      {{-- Danh mục con (nếu có) --}}
      @if($children->isNotEmpty())
  <div class="mb-3 d-flex flex-wrap gap-2">
    @foreach($children as $child)
      <a class="btn btn-outline-secondary btn-sm {{ $child->slug === $category->slug ? 'active' : '' }}"
         href="{{ route('users.products.byCategory', ['category' => $child->slug]) }}">
        {{ $child->category_name }}
      </a>
    @endforeach
  </div>
@endif

      {{-- Danh mục hiện tại --}}
      <li class="breadcrumb-item active" aria-current="page">
        {{ $category->category_name }}
      </li>
    </ol>
  </nav>
</div>


<div class="container py-5">
  {{-- Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">
        <i class="fas fa-sitemap me-2 text-secondary"></i>
        Danh mục: {{ $category->category_name }}
      </h1>
      <div class="text-muted">Tổng sản phẩm: {{ $products->total() }}</div>
    </div>

    {{-- (Tuỳ chọn) Sắp xếp nhanh --}}
    <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
      @foreach(request()->except(['sort','page']) as $k=>$v)
        @if(is_array($v))
          @foreach($v as $vv) <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}"> @endforeach
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
  </div>

  {{-- Grid sản phẩm --}}
    <div class="row g-4">
            @forelse($products as $product)
              @include('Users.partials.product-card', ['product' => $product])
            @empty
              <div class="col-12"><p class="text-center py-5 mb-0">Chưa có sản phẩm đang bán.</p></div>
            @endforelse
          </div>

  {{-- Phân trang --}}
  <div class="mt-4 d-flex justify-content-center">
    {{ $products->appends(request()->query())->links() }}
  </div>
</div>
@endsection

@extends('Users.layouts.home')

@section('title','Danh sách yêu thích')

@section('content')
<div class="container py-4">
  <h4 class="mb-4">Danh sách yêu thích</h4>

  <div class="row g-4">
    @forelse($products as $product)
      @include('Users.partials.product-card', ['product' => $product])
    @empty
      <div class="col-12 text-center text-muted py-5">
        Bạn chưa thêm sản phẩm nào vào yêu thích.
      </div>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $products->links() }}
  </div>
</div>
@endsection

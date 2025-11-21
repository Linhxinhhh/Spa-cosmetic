@extends('Users.layouts.home')

@section('content')

<style>




.capacity-chip{
  display:inline-block; padding:6px 14px; border-radius:9999px;
  background:#f3f4f6; color:#111827; text-decoration:none; border:1px solid transparent;
  transition:.15s; font-weight:500;
}
.capacity-chip:hover{ border-color:#fb923c; color:#fb923c; background:#fff; }
.capacity-chip.active{ border-color:#fb923c; color:#fb923c; background:#fff; }

.btn-action {
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #ddd;
  transition: all 0.3s ease;
}

.btn-action i {
  font-size: 16px;
  color: #555;
}

.btn-action:hover {
  background-color: #ff6a00;
  border-color: #ff6a00;
}

.btn-action:hover i {
  color: #fff;
}

.btn-action.active {
  background-color: #ff6a00;
  border-color: #ff6a00;
}

.btn-action.active i {
  color: #fff;
}


</style>
@php
  $isInWishlist = auth()->check() && auth()->user()->wishlist->contains($product->product_id);
@endphp
<div class="container py-5">
  <div class="row g-4">
  
    {{-- Cột trái: ảnh sản phẩm --}}
<div class="img-box col-md-5">
  <div class="border rounded p-3 position-relative text-center">
    <img id="mainPreview"
         src="{{ product_main_src($product) ?? asset('images/placeholder-4x3.jpg') }}"
         alt="{{ $product->product_name }}"
         class="img-fluid product-img"
         style="max-height: 400px; object-fit: contain;">


  </div>

{{-- Thumbnail list + Nút yêu thích --}}
<div class="d-flex align-items-center justify-content-between mt-3">
  {{-- Thumbnails --}}
  <div class="d-flex flex-wrap gap-2">
    @foreach($product->imagesRel as $img)
      <div class="border rounded p-1 thumb-item"
           style="width:70px; height:70px; cursor:pointer; overflow:hidden;">
        <img src="{{ $img->url }}"
             class="img-fluid h-100 w-100"
             style="object-fit: contain;"
             onclick="document.getElementById('mainPreview').src=this.src">
      </div>
    @endforeach
  </div>

 {{-- Nhóm nút yêu thích & so sánh --}}
<div class="d-flex align-items-center gap-2">
  {{-- Nút yêu thích --}}
  <button type="button"
          class="btn btn-light rounded-circle btn-action js-wishlist-btn {{ $isInWishlist ? 'active' : '' }}"
          data-url="{{ route('users.wishlist.toggle', $product->slug) }}"
          aria-pressed="{{ $isInWishlist ? 'true' : 'false' }}"
          title="{{ $isInWishlist ? 'Bỏ yêu thích' : 'Thêm yêu thích' }}">
    <i class="fas fa-heart"></i>
  </button>
{{-- Nút so sánh --}}
<form action="{{ route('users.compare.add', $product) }}" method="POST" class="m-0 p-0 d-inline-block">
  @csrf
  <button type="submit"
          class="btn btn-light rounded-circle btn-action"
          title="So sánh sản phẩm">
    <i class="fas fa-random"></i>
  </button>
</form>

</div>

</div>

     
</div>

    

    {{-- Cột phải: thông tin sản phẩm --}}
    <div class="col-md-7">
      <h4 class="fw-bold">{{ $product->product_name }}</h4>
      <p class="text-muted small mb-2">Mã sản phẩm: {{ $product->product_id }}</p>

      {{-- Giá --}}
      @php
        $final   = product_final_price($product);
        $orig    = (float)($product->price ?? 0);
        $hasSale = $final > 0 && $final < $orig;
      @endphp
      <div class="mb-3">
        @if($hasSale)
          <span class="fs-4 text-danger fw-bold">{{ number_format($final,0,',','.') }}₫</span>
          <del class="ms-2 text-muted">{{ number_format($orig,0,',','.') }}₫</del>
          <span class="badge bg-danger ms-2">-{{ round(100-($final/$orig*100)) }}%</span>
        @else
          <span class="fs-4 text-dark fw-bold">{{ number_format($final,0,',','.') }}₫</span>
        @endif
      </div>
@php
  $activeCap = trim((string)($product->capacity ?? ''));
@endphp

@if(($variants ?? collect())->count() > 0 || $activeCap !== '')
  <div class="mb-2">
    <span class="fw-semibold">Dung tích:</span>
    <span class="ms-1">{{ $activeCap !== '' ? $activeCap : 'Mặc định' }}</span>
  </div>

  <div class="d-flex flex-wrap gap-2 mt-2">
    @forelse($variants as $v)
      @php
        $cap = trim((string)($v->capacity ?? ''));
        $isActive = ($cap === $activeCap) || ($cap === '' && $activeCap === '');
      @endphp
      <a href="{{ route('users.products.show', $v->slug) }}"
         class="capacity-chip {{ $isActive ? 'active' : '' }}">
        {{ $cap !== '' ? $cap : 'Mặc định' }}
      </a>
    @empty
      {{-- Không có danh sách biến thể nhưng có capacity của chính sản phẩm --}}
      <span class="capacity-chip active">{{ $activeCap !== '' ? $activeCap : 'Mặc định' }}</span>
    @endforelse
  </div>
@endif




      {{-- Mô tả ngắn --}}
      <p>{{ Str::limit(strip_tags($product->description), 200) }}</p>

      {{-- Chọn số lượng --}}
      <div class="d-flex align-items-center mb-3">
        <span class="me-3">Số lượng:</span>
        <div class="input-group" style="width:150px;">
          <button class="btn btn-outline-secondary" type="button" onclick="qtyStep(-1)">-</button>
          <input type="number" id="qtyInput" name="quantity" value="1" min="1"
                 class="form-control text-center">
          <button class="btn btn-outline-secondary" type="button" onclick="qtyStep(1)">+</button>
        </div>
      </div>

      {{-- Nút --}}
      <div class="d-flex gap-3">
        <form action="{{ route('users.cart.add', $product) }}" method="POST">
          @csrf
          <input type="hidden" name="quantity" id="qtyHidden" value="1">
          <button type="submit" class="btn btn-primary btn-lg px-4">
            <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ
          </button>
        </form>
        <a href="#" class="btn btn-danger btn-lg px-4">
          Mua ngay
        </a>
      </div>
    </div>
  </div>
   <!-- Tabs thông tin thêm -->
  <div class="row mt-5">
    <div class="col-12">
      <ul class="nav nav-tabs" id="productTab" role="tablist">
        <li class="nav-item">
          <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button">Mô tả</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" id="spec-tab" data-bs-toggle="tab" data-bs-target="#spec" type="button">Thông số</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button">Đánh giá</button>
        </li>
      </ul>
      <div class="tab-content border p-4 rounded-bottom">
        <div class="tab-pane fade show active" id="desc">
          {!! nl2br(e($product->description)) !!}
        </div>
        <div class="tab-pane fade" id="spec">
          @if($product->specifications)
            <ul>
             @php
    $rawSpec = $product->specifications;

    // Nếu là JSON -> decode
    if (is_string($rawSpec) && str_starts_with(trim($rawSpec), '{')) {
        $specs = json_decode($rawSpec, true);
    }
    // Nếu là JSON array dạng ["a","b"]
    elseif (is_string($rawSpec) && str_starts_with(trim($rawSpec), '[')) {
        $specs = json_decode($rawSpec, true);
    }
    // Nếu là chuỗi nhiều dòng
    elseif (is_string($rawSpec) && str_contains($rawSpec, "\n")) {
        $lines = preg_split('/\r\n|\r|\n/', $rawSpec);
        $specs = [];
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $specs[] = $line;
            }
        }
    }
    // Nếu là chuỗi dùng dấu phẩy
    elseif (is_string($rawSpec) && str_contains($rawSpec, ',')) {
        $specs = array_map('trim', explode(',', $rawSpec));
    }
    // Nếu là chuỗi thường
    else {
        $specs = is_array($rawSpec) ? $rawSpec : [];
    }
@endphp

<div class="text-gray " id="spec">
    @php
        $capacity = $product->capacity ?? null;
    @endphp

    @if(!empty($capacity))
        <ul>
            <li><strong>Dung tích:</strong> {{ $capacity }}</li>
        </ul>
    @else
        <p class="text-muted">Không có thông số.</p>
    @endif
</div>



            </ul>
          @else
            <p class="text-muted">Chưa có thông số.</p>
          @endif
        </div>
        <div class="tab-pane fade" id="review">
          {{-- chèn reviews hoặc form bình luận --}}
          <p class="text-muted">Chưa có đánh giá nào.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- sản phẩm liên quan -->
  <div class="mt-5">
    <h4 class="mb-4">Sản phẩm liên quan</h4>
    <div class="row g-4">
      @foreach($relatedProducts as $p)
        @include('Users.partials.product-card', ['product' => $p, 'col' => 'col-6 col-md-4 col-lg-3'])
      @endforeach
    </div>
  </div>
</div>



@endsection
@push('scripts')
<script>
function qtyStep(delta) {
  let input = document.getElementById('qtyInput');
  let hidden = document.getElementById('qtyHidden');
  let val = parseInt(input.value) || 1;
  val = Math.max(1, val + delta);
  input.value = val;
  hidden.value = val;
}
</script>
<script>
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.js-wishlist-btn');
  if (!btn) return;

  e.preventDefault();

  const url   = btn.dataset.url;
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
      },
    });

    // chưa đăng nhập -> điều hướng login
    if (res.status === 401) {
      window.location.href = "{{ route('users.login') }}";
      return;
    }

    const data = await res.json(); // {status,message,count}

    if (data.status === 'added') {
      btn.classList.add('active');
      btn.setAttribute('aria-pressed', 'true');
    } else if (data.status === 'removed') {
      btn.classList.remove('active');
      btn.setAttribute('aria-pressed', 'false');
    }

    // nếu có badge tổng wishlist, update (tùy chọn)
    const badge = document.getElementById('wishlistCount');
    if (badge && typeof data.count !== 'undefined') {
      badge.textContent = data.count;
      badge.classList.toggle('d-none', data.count == 0);
    }

    // toast nhẹ (tùy chọn)
    // alert(data.message);

  } catch (err) {
    console.error(err);
    // alert('Có lỗi xảy ra, vui lòng thử lại!');
  }
});
</script>
@endpush

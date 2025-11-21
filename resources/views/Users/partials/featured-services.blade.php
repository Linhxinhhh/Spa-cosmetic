
@php
   use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    // ---- Helpers an toàn cho ảnh ----
    if (!function_exists('normalize_public_path')) {
        function normalize_public_path($path) {
            if (!$path) return null;
            $path = ltrim($path, '/');
            // bỏ tiền tố "storage/" hoặc "public/" nếu có
            $path = preg_replace('#^(storage/|public/)#', '', $path);
            return $path;
        }
    }

    if (!function_exists('pick_first_image')) {
        function pick_first_image($val) {
            if (!$val) return null;
            // Nếu là JSON array -> lấy phần tử đầu
            if (is_string($val) && str_starts_with(trim($val), '[')) {
                $arr = json_decode($val, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($arr) && count($arr)) {
                    return $arr[0];
                }
            }
            // Nếu là mảng -> lấy phần tử đầu
            if (is_array($val)) return $val[0] ?? null;
            return $val; // string thường
        }
    }

    if (!function_exists('service_img_src')) {
        function service_img_src($service) {
            $imgPath = $service->thumbnail ?: $service->images;
            $imgPath = pick_first_image($imgPath);

            if ($imgPath) {
                // full URL?
                if (\Illuminate\Support\Str::startsWith($imgPath, ['http://', 'https://', '//'])) {
                    return $imgPath;
                }
                $imgPath = normalize_public_path($imgPath);

                // file có thật trong disk public?
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)) {
                    return \Illuminate\Support\Facades\Storage::url($imgPath); // => /storage/...
                }

                // fallback: asset (trường hợp ảnh nằm sẵn trong /public)
                return asset($imgPath);
            }
            return asset('images/placeholder-4x3.jpg');
        }
    }

if (!function_exists('category_img_src')) {
    function category_img_src($cat) {
        $imgPath = $cat->image ?? null;
        if ($imgPath) {
            if (\Illuminate\Support\Str::startsWith($imgPath, ['http://', 'https://', '//'])) {
                return $imgPath;
            }
            $imgPath = ltrim($imgPath, '/');
            $imgPath = preg_replace('#^(storage/|public/)#', '', $imgPath);

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)) {
                return \Illuminate\Support\Facades\Storage::url($imgPath);
            }
            return asset($imgPath);
        }
        return asset('images/price-banner.jpg');
    }
}
@endphp




<section class="container py-5">
  {{-- ======= Tabs header with sliding indicator ======= --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <h2 class="m-0 fw-extrabold">Dịch vụ & Bảng giá</h2>
  </div>

  <ul class="nav nav-pills gap-2 mb-3 position-relative fancy-tabs" id="svcTabs" role="tablist" style="--tab-color:#0ea5e9;">
    <span class="tab-indicator"></span>
    <li class="nav-item" role="presentation">
      <button class="nav-link active fw-semibold px-4 py-2" id="tab-featured" data-bs-toggle="tab" data-bs-target="#pane-featured" type="button" role="tab" aria-controls="pane-featured" aria-selected="true">
        Dịch vụ nổi bật
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link fw-semibold px-4 py-2" id="tab-pricelist" data-bs-toggle="tab" data-bs-target="#pane-pricelist" type="button" role="tab" aria-controls="pane-pricelist" aria-selected="false">
        Bảng giá
      </button>
    </li>
  </ul>

  <div class="tab-content">

    {{-- ======= TAB 1: Featured ======= --}}
   
    <div class="tab-pane fade show active" id="pane-featured" role="tabpanel" aria-labelledby="tab-featured">
      @if($featuredServices->count())
        <div class="row">
          @foreach($featuredServices as $service)
            @php
              // ảnh
              $imgSrc = function_exists('service_img_src') ? service_img_src($service) : asset('images/placeholder-4x3.jpg');
              // giá
              $orig     = $service->price_original ?? $service->price ?? 0;
              $sale     = $service->price_sale;
              $hasSale  = $sale && $sale > 0 && $orig > $sale;
              $final    = $hasSale ? $sale : $orig;
              $discount = ($hasSale && $orig>0) ? round(100 - ($final/$orig*100)) : 0;
            @endphp

            <div class="col-12 col-sm-6 col-lg-3 mb-4">
              <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="position-relative">
                  <img src="{{ $imgSrc }}" alt="{{ $service->service_name }}" class="w-100" style="height:180px;object-fit:cover;">
                  @if($service->category)
                    <span class="badge position-absolute" style="top:10px;left:10px;background:#dbeafe;color:#1e40af;">
                      {{ $service->category->category_name }}
                    </span>
                  @endif
                  @if($hasSale)
                    <span class="badge position-absolute" style="top:10px;right:10px;background:#fee2e2;color:#b91c1c;">
                      -{{ $discount }}%
                    </span>
                  @endif
                </div>

                <div class="card-body d-flex flex-column">
                  <h5 class="mb-1 fw-bold">{{ $service->service_name }}</h5>
                  @if($service->short_desc)
                    <p class="text-muted mb-2">{{ Str::limit($service->short_desc, 80) }}</p>
                  @endif

                  <div class="mb-3">
                    @if($hasSale)
                      <div class="fw-bold" style="font-size:1.125rem;color:#0ea5e9;">
                        {{ number_format($final,0,',','.') }}đ
                      </div>
                      <div class="text-muted" style="text-decoration:line-through;">
                        {{ number_format($orig,0,',','.') }}đ
                      </div>
                    @else
                      <div class="fw-bold" style="font-size:1.125rem;color:#1e40af;">
                        {{ number_format($final,0,',','.') }}đ
                      </div>
                    @endif
                  </div>

                  <div class="mt-auto d-grid gap-2">
                    <a href="{{ route('users.booking.create', ['service' => $service->slug]) }}"
                       class="btn btn-primary rounded-3">
                      <i class="fas fa-calendar-check me-2"></i>Đặt lịch ngay
                    </a>
                    <a href="{{ route('users.services.show', $service->slug) }}"
                       class="btn btn-outline-secondary rounded-3">
                      Chi tiết
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-muted">Chưa có dịch vụ nổi bật.</div>
      @endif
    </div>

    {{-- ======= TAB 2: Price list by category ======= --}}
  {{-- ======= TAB 2: Price list by category (giống ảnh) ======= --}}
<div class="tab-pane fade" id="pane-pricelist" role="tabpanel" aria-labelledby="tab-pricelist">
  @forelse(($priceCategories ?? collect()) as $cat)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-3 p-md-4">
        <div class="row g-4 align-items-stretch">
          {{-- LEFT: banner --}}
          <div class="col-lg-5">
            <div class="rounded-4 overflow-hidden h-100">
              <img
              src="{{ category_img_src($cat) }}"
              alt="{{ $cat->category_name }}"
              class="w-100 h-100" style="object-fit:cover;">

            </div>
          </div>

          {{-- RIGHT: title + list + button --}}
          <div class="col-lg-7">
            <h4 class="text-center fw-bold text-success text-uppercase mb-3" style="letter-spacing:.3px;">
              {{ $cat->category_name }}
            </h4>

            <div class="list-unstyled m-0">
              @foreach($cat->services as $sv)
                @php
                  $orig = $sv->price_original ?? $sv->price ?? 0;
                  $sale = $sv->price_sale;
                  $final = ($sale && $sale>0 && $orig>$sale) ? $sale : $orig;
                @endphp
           <div class="d-flex align-items-center py-3 border-top price-row">
  <a href="{{ route('users.services.show', $sv->slug) }}"
     class="text-decoration-none text-dark flex-grow-1 pe-3">
    {{ $sv->service_name }}
  </a>
  <div class="dots flex-grow-1 d-none d-md-block"></div>
  <div class="ms-auto fw-bold text-orange">
    {{ number_format($final,0,',','.') }} <span class="small fw-semibold">đ</span>
  </div>
</div>

              @endforeach
            </div>

            <div class="text-center mt-4">
              {{-- FIXED: dùng $cat, không dùng $sv ngoài foreach --}}
             <a href="{{route('users.services.show', ['service' => $sv->slug])}}" {{ $sv->service_name }} class="btn rounded-pill px-5 py-2 btn-see-all "> Xem tất cả </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="text-muted">Chưa có bảng giá.</div>
  @endforelse
</div>

  </div>
</section>

{{-- ======= Styles (nhẹ, hiện đại) ======= --}}
<style>
  .fw-extrabold{font-weight:800}
  .object-cover{object-fit:cover}

  .text-indigo{color:#3730a3}
  .text-cyan{color:#155e75}
  .text-orange{color:#ff7a00}

  /* tab indicator */
  .fancy-tabs .tab-indicator{
    position:absolute; bottom:-2px; left:0; height:3px; width:0; border-radius:6px;
    background:var(--tab-color, #0ea5e9); transition:all .3s ease;
  }
  .fancy-tabs .nav-link{border-radius:12px;}
  .fancy-tabs .nav-link.active{background:#e6f4ff; color:#0b6bcb;}

  /* featured card hover */
  .svc-card{transition:transform .2s ease, box-shadow .2s ease;}
  .svc-card:hover{transform:translateY(-3px); box-shadow:0 12px 24px rgba(0,0,0,.08);}

  /* dotted leader line */
  .svc-line .dots{border-bottom:1px dotted #dcdcdc; height:0;}
  .svc-line .name{max-width:70%;}
  @media (max-width:576px){
    .svc-line .name{max-width:60%;}
  }
  .price-row .dots{
  border-bottom:1px dotted #d9d9d9;
  height:0;
  margin:0 12px;
}
.btn-see-all{
  background:#d9f0e3;   /* xanh nhạt */
  color:#2f7b57;
  font-weight:700;
}
.btn-see-all:hover{ filter:brightness(.97); color:#2b6f50; }
</style>

{{-- ======= JS: move indicator under active tab; open by hash ======= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const nav = document.querySelector('#svcTabs');
  const indicator = nav.querySelector('.tab-indicator');
  function moveIndicator(btn){
    const r = btn.getBoundingClientRect(), nr = nav.getBoundingClientRect();
    indicator.style.width = r.width + 'px';
    indicator.style.left = (r.left - nr.left) + 'px';
  }
  const firstActive = nav.querySelector('.nav-link.active'); if (firstActive) moveIndicator(firstActive);
  nav.querySelectorAll('.nav-link').forEach(b=>{
    b.addEventListener('shown.bs.tab', e => moveIndicator(e.target));
  });

  // Optional: open tab via hash
  if (location.hash === '#pricing') {
    const t = document.getElementById('tab-pricelist');
    if (t) new bootstrap.Tab(t).show();
  }
});
</script>

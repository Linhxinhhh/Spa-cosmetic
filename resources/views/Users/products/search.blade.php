@extends('Users.layouts.home')

@section('title','Tìm kiếm sản phẩm')

@push('styles')
<style>
  .sidebar-card{border:0;border-radius:16px;box-shadow:0 8px 22px rgba(0,0,0,.07)}
  .result-card{border:0;border-radius:18px;box-shadow:0 8px 22px rgba(0,0,0,.07);transition:.2s}
  .result-card:hover{transform:translateY(-2px);box-shadow:0 12px 28px rgba(0,0,0,.1)}
  .sticky-md{position:sticky; top:90px}
  .hl{background:#fff3cd;padding:.05em .2em;border-radius:.25rem}

 
</style>
@endpush

@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

function product_img_src($item){
  $p = $item->thumbnail ?: $item->images;
  if (!$p) return asset('images/placeholder-4x3.jpg');
  if (Str::startsWith($p,['http://','https://','//'])) return $p;
  $p = ltrim(preg_replace('#^(public/|storage/)#','',$p),'/');
  return Storage::disk('public')->exists($p) ? Storage::url($p) : asset($p);
}
/** Chuẩn hóa đường dẫn ảnh (storage/public/URL tuyệt đối) */
function _asset_from_mixed($path){
  if (!$path) return null;
  if (Str::startsWith($path, ['http://','https://','//'])) return $path;
  $p = ltrim(preg_replace('#^(public/|storage/)#','', $path), '/');
  return Storage::disk('public')->exists($p) ? Storage::url($p) : asset($p);
}

/** Lấy mảng ảnh từ:
 * - Quan hệ imagesRel (ưu tiên)
 * - Cột images dạng JSON/CSV
 * - Cuối cùng là thumbnail
 */


// preset đang chọn (để render đúng UI)
$min = request('price_min'); $max = request('price_max');
$rangeKey = '';
if ($min === null && $max === null)            $rangeKey = '';
elseif ($min == 0 && $max == 200000)           $rangeKey = '0-200000';
elseif ($min == 200000 && $max == 500000)      $rangeKey = '200000-500000';
elseif ($min == 500000 && $max == 1000000)     $rangeKey = '500000-1000000';
elseif ($min == 1000000 && $max == 2000000)    $rangeKey = '1000000-2000000';
elseif ($min == 2000000 && ($max === null || $max === '')) $rangeKey = '2000000-';
else                                           $rangeKey = 'custom';
@endphp

@section('content')
<div class="container py-4">
  <div class="row g-4">
    {{-- ================== SIDEBAR (bên trái) ================== --}}
    <aside class="col-12 col-lg-3">
      <div class="card sidebar-card sticky-md">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Bộ lọc</h5>

          <form method="GET" action="{{ route('users.products.search') }}" class="vstack gap-3">

            {{-- Từ khóa --}}
            <div>
              <label class="form-label">Từ Khóa</label>
              <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tên sản phẩm / mô tả...">
            </div>

            {{-- Danh mục --}}
            <div>
              <label class="form-label">Danh Mục</label>
              <select name="category_id" class="form-select">
                <option value="">-- Tất cả --</option>
                @foreach($categories as $c)
                  <option value="{{ $c->category_id }}" {{ request('category_id')==$c->category_id?'selected':'' }}>
                    {{ $c->category_name }}
                  </option>
                @endforeach
              </select>
            </div>

              <div>
              <label class="form-label">Thương Hiệu</label>
              <select name="brand_id" class="form-select">
                <option value="">-- Tất cả --</option>
                @foreach($brands as $c)
                  <option value="{{ $c->brand_id }}" {{ request('brand_id')==$c->brand_id?'selected':'' }}>
                    {{ $c->brand_name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Giá: preset + custom --}}
            <div>
              <label class="form-label">Khoảng Giá</label>
              <select id="pricePreset" class="form-select mb-2">
                <option value="" {{ $rangeKey==='' ? 'selected' : '' }}>Tất cả mức giá</option>
                <option value="0-200000" {{ $rangeKey==='0-200000' ? 'selected' : '' }}>Dưới 200k</option>
                <option value="200000-500000" {{ $rangeKey==='200000-500000' ? 'selected' : '' }}>200k – 500k</option>
                <option value="500000-1000000" {{ $rangeKey==='500000-1000000' ? 'selected' : '' }}>500k – 1 triệu</option>
                <option value="1000000-2000000" {{ $rangeKey==='1000000-2000000' ? 'selected' : '' }}>1 – 2 triệu</option>
                <option value="2000000-" {{ $rangeKey==='2000000-' ? 'selected' : '' }}>Trên 2 triệu</option>
                <option value="custom" {{ $rangeKey==='custom' ? 'selected' : '' }}>Tùy chọn…</option>
              </select>

              {{-- hidden để submit --}}
              <input type="hidden" name="price_min" id="priceMinHidden" value="{{ $min }}">
              <input type="hidden" name="price_max" id="priceMaxHidden" value="{{ $max }}">

              {{-- custom chỉ hiện khi chọn "Tùy chọn…" --}}
              <div id="customPriceRow" class="d-flex gap-2 {{ $rangeKey==='custom' ? '' : 'd-none' }}">
                <input type="number" id="priceMin" value="{{ $min }}" class="form-control" placeholder="Từ">
                <input type="number" id="priceMax" value="{{ $max }}" class="form-control" placeholder="Đến">
              </div>
            </div>

            {{-- Nút --}}
            <div class="d-grid gap-2">
              <button class="btn btn-primary"><i class="fas fa-search me-1"></i> Áp dụng</button>
              <a href="{{ route('users.products.search') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>

            {{-- Giữ tham số sort hiện tại (nếu có) --}}
            @if(request('sort'))
              <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
          </form>
        </div>
      </div>
    </aside>
    
    {{-- ================== KẾT QUẢ (bên phải) ================== --}}


    <section class="col-lg-9">
      <div class="row g-4">
        @forelse($items as $product)
         @include('Users.partials.product-card', [
            'product' => $product,
            'col' => 'col-md-6 col-lg-4 col-xl-3'
          ])

        @empty
          <div class="col-12 text-center text-muted py-5">Không có kết quả</div>
        @endforelse
      </div>

      <div class="mt-4 d-flex justify-content-center">
        {{ $items->onEachSide(1)->appends(request()->query())->links() }}
      </div>
    </section>


  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const preset   = document.getElementById('pricePreset');
  const row      = document.getElementById('customPriceRow');
  const minUI    = document.getElementById('priceMin');
  const maxUI    = document.getElementById('priceMax');
  const minHid   = document.getElementById('priceMinHidden');
  const maxHid   = document.getElementById('priceMaxHidden');

  function setHidden(minVal, maxVal){
    minHid.value = (minVal ?? '').toString();
    maxHid.value = (maxVal ?? '').toString();
  }
  function applyPreset(val){
    if (!val){ row.classList.add('d-none'); setHidden('',''); return; }
    if (val === 'custom'){ row.classList.remove('d-none'); setHidden(minUI?.value, maxUI?.value); return; }
    const [a,b] = val.split('-');
    row.classList.add('d-none');
    setHidden(a || '', b || '');
  }

  applyPreset(preset.value);
  preset.addEventListener('change', ()=>applyPreset(preset.value));
  [minUI, maxUI].forEach(el => el?.addEventListener('input', ()=>{
    if (preset.value === 'custom') setHidden(minUI.value, maxUI.value);
  }));
});
</script>
@endpush

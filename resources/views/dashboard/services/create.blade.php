@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị dịch vụ')
@section('page-title', 'Thêm dịch vụ')

@push('styles')

<link href="{{asset('admin/giaodien/css/style.css')}}" rel="stylesheet">
@endpush
<style>
  .create-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(30, 64, 175, 0.2);
    }
    
</style>
@section('content')
<div class="container-fluid">
  {{-- Header --}}
    <div class="create-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size: 2.5rem; font-weight: 700;">
                    <i class="fas fa-plus-circle mr-3"></i>Thêm dịch vụ mới
                </h1>
                <p class="mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                    Điền thông tin chi tiết để thêm sản phẩm vào hệ thống
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('admin.services.index') }}" class="btn btn-cancel">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

  {{-- Errors --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  {{-- Form --}}
  <div class="form-container">
    <form id="serviceForm" action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      {{-- Thông tin cơ bản --}}
      <div class="form-card">
        <h3 class="section-title"><i class="fas fa-info-circle mr-2"></i>Thông tin cơ bản</h3>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-concierge-bell mr-1"></i>Tên dịch vụ <span class="required-mark">*</span></label>
          <div class="input-icon">
            <i class="fas fa-spa"></i>
            <input type="text" name="service_name"
                   class="form-control form-control-modern @error('service_name') is-invalid @enderror"
                   value="{{ old('service_name') }}" required>
          </div>
          @error('service_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-align-left mr-1"></i>Mô tả ngắn</label>
          <div class="input-icon">
            <i class="fas fa-quote-left"></i>
            <input type="text" name="short_desc"
                   class="form-control form-control-modern @error('short_desc') is-invalid @enderror"
                   value="{{ old('short_desc') }}">
          </div>
          @error('short_desc')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-layer-group mr-1"></i>Danh mục <span class="required-mark">*</span></label>
          <div class="input-icon">
            <i class="fas fa-list"></i>
            <select name="category_id"
                    class="form-control form-control-modern @error('category_id') is-invalid @enderror" required>
              <option value="">-- Chọn danh mục --</option>
              @foreach($categories as $category)
                <option value="{{ $category->category_id }}"
                  {{ old('category_id')==$category->category_id? 'selected':'' }}>
                  {{ $category->category_name }}
                </option>
              @endforeach
            </select>
          </div>
          @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label"><i class="fas fa-tags mr-1"></i>Loại *</label>
              <div class="input-icon">
                <i class="fas fa-tag"></i>
                <select name="type" class="form-control form-control-modern @error('type') is-invalid @enderror" required>
                  <option value="Lẻ" {{ old('type','Lẻ')=='Lẻ'?'selected':'' }}>Lẻ</option>
                  <option value="Gói"  {{ old('type')=='Gói'?'selected':'' }}>Gói</option>
                </select>
              </div>
              @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label"><i class="fas fa-link mr-1"></i>Slug (tùy chọn)</label>
              <div class="input-icon">
                <i class="fas fa-link"></i>
                <input type="text" name="slug"
                       class="form-control form-control-modern @error('slug') is-invalid @enderror"
                       placeholder="Bỏ trống để tự sinh"
                       value="{{ old('slug') }}">
              </div>
              @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Giá & Thời gian --}}
      <div class="form-card">
        <h3 class="section-title"><i class="fas fa-money-bill-wave mr-2"></i>Giá & thời gian</h3>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Giá gốc (VND)</label>
              <div class="input-icon">
                <i class="fas fa-money-bill"></i>
                <input type="number" name="price_original" min="0" step="1"
                       class="form-control form-control-modern @error('price_original') is-invalid @enderror"
                       value="{{ old('price_original') }}">
              </div>
              @error('price_original')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Giá khuyến mãi (VND)</label>
              <div class="input-icon">
                <i class="fas fa-percent"></i>
                <input type="number" name="price_sale" min="0" step="1"
                       class="form-control form-control-modern @error('price_sale') is-invalid @enderror"
                       value="{{ old('price_sale') }}" placeholder="Để trống nếu không giảm">
              </div>
              @error('price_sale')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- (Optional) giá decimal cũ nếu bạn vẫn dùng ở nơi khác --}}
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Giá (thừa kế cũ)</label>
              <div class="input-icon">
                <i class="fas fa-coins"></i>
                <input type="number" name="price" min="0" step="0.01"
                       class="form-control form-control-modern @error('price') is-invalid @enderror"
                       value="{{ old('price') }}">
              </div>
              @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Thời lượng (phút) *</label>
              <div class="input-icon">
                <i class="fas fa-stopwatch"></i>
                <input type="number" name="duration" id="duration" min="1" required
                       class="form-control form-control-modern @error('duration') is-invalid @enderror"
                       value="{{ old('duration') }}">
              </div>
              @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror

              <div class="duration-preview" id="durationPreview">
                <small class="text-primary font-weight-bold">
                  <i class="fas fa-info-circle mr-1"></i>
                  Thời lượng: <span id="durationDisplay">0 phút</span>
                  (<span id="hourDisplay">0 phút</span>)
                </small>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Trạng thái *</label>
            <div class="input-icon">
              <i class="fas fa-toggle-on"></i>
              <select name="status" class="form-control form-control-modern @error('status') is-invalid @enderror" required>
                <option value="1" {{ old('status',1)==1?'selected':'' }}>Hoạt động</option>
                <option value="0" {{ old('status')===0?'selected':'' }}>Tạm ngưng</option>
              </select>
            </div>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <div class="form-check mt-3">
              <input type="checkbox" id="is_featured" name="is_featured" value="1"
                     class="form-check-input" {{ old('is_featured')?'checked':'' }}>
              <label class="form-check-label" for="is_featured">Đánh dấu Nổi bật</label>
            </div>
          </div>
        </div>
      </div>

   
{{-- Mô tả & Hình ảnh --}}
<div class="form-card">
    <h3 class="section-title"><i class="fas fa-images mr-2"></i>Mô tả & hình ảnh</h3>

    <div class="form-group mb-4">
        <label class="form-label"><i class="fas fa-paragraph mr-1"></i>Mô tả dịch vụ</label>
        <textarea name="description" rows="6" class="form-control form-control-modern @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <label class="form-label"><i class="fas fa-image mr-1"></i>Ảnh đại diện</label>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="form-control" required>
            <small class="text-muted">Ảnh chính hiển thị ở danh sách và chi tiết</small>
            @error('thumbnail')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="col-lg-6">
            <label class="form-label"><i class="fas fa-images mr-1"></i>Ảnh chi tiết (tối đa 6 ảnh)</label>
            <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple class="form-control">
            <small class="text-muted">Giữ Ctrl/Cmd để chọn nhiều ảnh</small>
            @error('gallery')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>


      {{-- Actions --}}
      <div class="button-group">
        <button type="submit" class="btn btn-save">
          <i class="fas fa-save mr-2"></i>Lưu dịch vụ
        </button>
        <a href="{{ route('admin.services.index') }}" class="btn btn-cancel">
          <i class="fas fa-times mr-2"></i>Hủy bỏ
        </a>
      </div>
    </form>
  </div>
</div>
@endsection




@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  bindPreview('thumbnail');
  bindPreview('images');

  function bindPreview(id){
    const input = document.getElementById(id);
    const label = document.querySelector(`label.file-upload-area[for="${id}"]`);
    if(!input || !label) return;
    const previewWrap = label.querySelector('.preview-ui');
    const uploadUi   = label.querySelector('.upload-ui');
    const img        = label.querySelector('.preview-img');

    input.addEventListener('change', () => {
      const f = input.files?.[0]; if (!f) return;
      img.src = URL.createObjectURL(f);
      previewWrap.classList.remove('d-none');
      uploadUi.classList.add('d-none');
    });
  }
});
</script>
@endpush





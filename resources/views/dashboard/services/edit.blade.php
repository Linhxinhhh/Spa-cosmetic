@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị dịch vụ')
@section('page-title', 'Chỉnh sửa dịch vụ')

@push('styles')
<link href="{{asset('admin/giaodien/css/style.css')}}" rel="stylesheet">
@endpush


@section('content')
<div class="container-fluid">
  {{-- Header --}}
  <div class="edit-header">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="mb-2" style="font-weight:700">
          <i class="fas fa-edit mr-2"></i>Chỉnh sửa dịch vụ
        </h1>
        <p class="mb-2" style="opacity:.9">Cập nhật thông tin dịch vụ trong hệ thống</p>
        <div class="service-id-badge">
          <i class="fas fa-hashtag mr-1"></i>ID: {{ $service->service_id }}
        </div>
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
    <form id="serviceEditForm"
          action="{{ route('admin.services.update', $service->service_id) }}"
          method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')

      {{-- Thông tin cơ bản --}}
      <div class="form-card">
        <h3 class="section-title"><i class="fas fa-info-circle mr-2"></i>Thông tin cơ bản</h3>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-concierge-bell mr-1"></i>Tên dịch vụ <span class="required-mark">*</span>
          </label>
          <div class="input-icon">
            <i class="fas fa-spa"></i>
            <input type="text" name="service_name"
                   class="form-control form-control-modern @error('service_name') is-invalid @enderror"
                   value="{{ old('service_name', $service->service_name) }}" required>
          </div>
          @error('service_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-align-left mr-1"></i>Mô tả ngắn</label>
          <div class="input-icon">
            <i class="fas fa-quote-left"></i>
            <input type="text" name="short_desc"
                   class="form-control form-control-modern @error('short_desc') is-invalid @enderror"
                   value="{{ old('short_desc', $service->short_desc) }}">
          </div>
          @error('short_desc')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-list mr-1"></i>Danh mục <span class="required-mark">*</span></label>
          <div class="input-icon">
            <i class="fas fa-layer-group"></i>
            <select name="category_id"
                    class="form-control form-control-modern @error('category_id') is-invalid @enderror" required>
              <option value="">-- Chọn danh mục --</option>
              @foreach($categories as $category)
                <option value="{{ $category->category_id }}"
                  {{ old('category_id', $service->category_id)==$category->category_id?'selected':'' }}>
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
                  <option value="Lẻ" {{ old('type', $service->type)=='Lẻ'?'selected':'' }}>Lẻ</option>
                  <option value="Gói"  {{ old('type', $service->type)=='Gói' ?'selected':'' }}>Gói</option>
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
                       value="{{ old('slug', $service->slug) }}">
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
                       value="{{ old('price_original', $service->price_original) }}">
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
                       value="{{ old('price_sale', $service->price_sale) }}"
                       placeholder="Để trống nếu không giảm">
              </div>
              @error('price_sale')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- (Optional) giá cũ kiểu decimal(10,2) nếu bạn còn dùng --}}
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Giá (thừa kế cũ)</label>
              <div class="input-icon">
                <i class="fas fa-coins"></i>
                <input type="number" name="price" min="0" step="0.01"
                       class="form-control form-control-modern @error('price') is-invalid @enderror"
                       value="{{ old('price', $service->price) }}">
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
                       value="{{ old('duration', $service->duration) }}">
              </div>
              @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror

              <div class="duration-preview" id="durationPreview">
                <small class="text-primary font-weight-bold">
                  <i class="fas fa-info-circle mr-1"></i>
                  Thời lượng: <span id="durationDisplay">{{ $service->duration }} phút</span>
                  (<span id="hourDisplay">
                    @php $h=floor($service->duration/60); $m=$service->duration%60; @endphp
                    {{ $h>0 ? $h.' giờ '.$m.' phút' : $m.' phút' }}
                  </span>)
                </small>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Trạng thái *</label>
            <div class="input-icon">
              <i class="fas fa-toggle-on"></i>
              <select name="status" class="form-control form-control-modern @error('status') is-invalid @enderror" required>
                <option value="1" {{ old('status', $service->status)==1?'selected':'' }}>Hoạt động</option>
                <option value="0" {{ old('status', $service->status)==0?'selected':'' }}>Tạm ngưng</option>
              </select>
            </div>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <div class="form-check mt-3">
              <input type="checkbox" id="is_featured" name="is_featured" value="1"
                     class="form-check-input" {{ old('is_featured', $service->is_featured)?'checked':'' }}>
              <label class="form-check-label" for="is_featured">Đánh dấu Nổi bật</label>
            </div>
          </div>
        </div>
      </div>

      {{-- Mô tả & Hình ảnh --}}
      <div class="form-card">
        <h3 class="section-title"><i class="fas fa-cog mr-2"></i>Mô tả & hình ảnh</h3>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-paragraph mr-1"></i>Mô tả dịch vụ</label>
          <textarea name="description" rows="6"
                    class="form-control form-control-modern @error('description') is-invalid @enderror">{{ old('description', $service->description) }}</textarea>
          @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
            @php
            use Illuminate\Support\Facades\Storage;
            use Illuminate\Support\Str;
            
            // Thumbnail
            $thumb = $service->thumbnail;
            $thumbSrc = null;
            if ($thumb) {
                $thumbSrc = Storage::disk('public')->exists($thumb) ? Storage::url($thumb)
                        : (Str::startsWith($thumb, ['http://','https://','//']) ? $thumb : asset($thumb));
            }

            // Images (ảnh phụ)
            $img = $service->images;
            $imgSrc = null;
            if ($img) {
                $imgSrc = Storage::disk('public')->exists($img) ? Storage::url($img)
                        : (Str::startsWith($img, ['http://','https://','//']) ? $img : asset($img));
            }
        @endphp
                  <label  class="form-label"><i class="fas fa-image mr-1"></i>
Ảnh đại diện</label>
          @if($thumbSrc)
        <div class="current-image">
            <img src="{{ $thumbSrc }}" alt="thumb">
            <p class="mt-2 mb-0 text-primary font-weight-bold">Ảnh đại diện hiện có</p>
        </div>
        @endif
  

        {{-- Fallback KHÔNG CẦN JS --}}
        
        <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="d-none">
        <label for="thumbnail" class="btn btn-sm btn-outline-primary mt-2 text-white">Chọn ảnh </label>
        @error('thumbnail')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror


          {{-- Images (phụ) --}}
                    <label style="margin-top:10px" class="form-label"><i class="fas fa-image mr-1"></i>
Ảnh chi tiết</label>
         @if($imgSrc)
        <div style="margin-top:10px;" class="current-image">
            <img src="{{ $imgSrc }}" alt="image">
            <p class="mt-2 mb-0 text-primary font-weight-bold">Ảnh chi tiết hiện có</p>
        </div>
        @endif
        
{{-- Fallback KHÔNG CẦN JS --}}
<input type="file" id="images" name="images" accept="image/*" class="d-none">
<label for="images" class="btn btn-sm btn-outline-primary mt-2 text-white">Chọn ảnh</label>
@error('images')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

        </div>
      </div>

      <div class="button-group">
        <button class="btn btn-update" type="submit">
          <i class="fas fa-save mr-2"></i>Cập nhật dịch vụ
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
<script src="{{asset('admin/giaodien/js/main.js')}}"></script>
@endpush

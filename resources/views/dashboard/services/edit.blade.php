@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị dịch vụ')
@section('page-title', 'Chỉnh sửa dịch vụ')

@push('styles')
<style>
    .create-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(30, 64, 175, 0.2);
    }
    
    .form-container {
        background: white;
        padding: 2.5rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        color: #1e40af;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
    }
    
    .form-control-modern {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f9fafb;
        width: 100%;
    }
    
    .form-control-modern:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: white;
        outline: none;
    }
    
    .form-control-modern.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        padding: 15px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        min-width: 150px;
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
    }
    
    .btn-cancel {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        border: none;
        color: white;
        padding: 15px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        min-width: 150px;
        text-align: center;
    }
    
    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
        color: white;
        text-decoration: none;
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
    }
    
    .input-icon {
        position: relative;
    }
    
    .input-icon i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        z-index: 10;
    }
    
    .input-icon input,
    .input-icon select,
    .input-icon textarea {
        padding-left: 45px;
    }
    
    .section-title {
        color: #1e40af;
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid #dbeafe;
        display: flex;
        align-items: center;
    }
    
    .file-upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    
    .file-upload-area:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    
    .file-upload-area.dragover {
        border-color: #10b981;
        background: #ecfdf5;
    }
    
    .required-mark {
        color: #ef4444;
        font-weight: bold;
    }
    
    .button-group {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #f3f4f6;
    }
    
    .price-preview {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .price-original {
        font-size: 1.1rem;
        color: #6b7280;
        text-decoration: line-through;
    }
    
    .price-final {
        font-size: 1.3rem;
        color: #dc2626;
        font-weight: 700;
    }
    
    .form-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .current-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        margin-bottom: 1rem;
    }
    
    .duration-preview {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .image-thumbnail {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        margin: 5px;
    }
    
    .gallery-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    
    .delete-checkbox {
        position: absolute;
        top: 5px;
        right: 5px;
        background: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="create-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size: 2.5rem; font-weight: 700;">
                    <i class="fas fa-edit mr-3"></i>Chỉnh sửa dịch vụ
                </h1>
                <p class="mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                    Cập nhật thông tin dịch vụ trong hệ thống
                </p>
                <div class="mt-3 bg-white text-primary d-inline-block px-3 py-2 rounded" style="border-radius: 20px !important;">
                    <i class="fas fa-hashtag mr-2"></i>ID: {{ $service->service_id }}
                </div>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('admin.services.index') }}" class="btn btn-cancel">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="form-container">
        <form action="{{ route('admin.services.update', $service->service_id) }}" method="POST" enctype="multipart/form-data" id="serviceForm">
            @csrf
            @method('PUT')

            {{-- Thông tin cơ bản --}}
            <div class="form-card">
                <h3 class="section-title">
                    <i class="fas fa-info-circle mr-2"></i>Thông tin cơ bản
                </h3>

                {{-- Tên dịch vụ --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-concierge-bell mr-1"></i>Tên dịch vụ <span class="required-mark">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-spa"></i>
                        <input type="text" 
                               name="service_name"
                               class="form-control form-control-modern @error('service_name') is-invalid @enderror"
                               value="{{ old('service_name', $service->service_name) }}" 
                               placeholder="Nhập tên dịch vụ..."
                               required>
                    </div>
                    @error('service_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mô tả ngắn --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left mr-1"></i>Mô tả ngắn
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-quote-left"></i>
                        <input type="text" 
                               name="short_desc"
                               class="form-control form-control-modern @error('short_desc') is-invalid @enderror"
                               value="{{ old('short_desc', $service->short_desc) }}"
                               placeholder="Mô tả ngắn gọn về dịch vụ...">
                    </div>
                    @error('short_desc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    {{-- Danh mục --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-layer-group mr-1"></i>Danh mục <span class="required-mark">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-list"></i>
                                <select name="category_id"
                                        class="form-control form-control-modern @error('category_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}"
                                            {{ old('category_id', $service->category_id) == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Loại dịch vụ --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-tags mr-1"></i>Loại dịch vụ <span class="required-mark">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-tag"></i>
                                <select name="type" 
                                        class="form-control form-control-modern @error('type') is-invalid @enderror" 
                                        required>
                                    <option value="Lẻ" {{ old('type', $service->type) == 'Lẻ' ? 'selected' : '' }}>Lẻ</option>
                                    <option value="Gói" {{ old('type', $service->type) == 'Gói' ? 'selected' : '' }}>Gói</option>
                                </select>
                            </div>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Slug --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-link mr-1"></i>Slug (tùy chọn)
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-link"></i>
                        <input type="text" 
                               name="slug"
                               class="form-control form-control-modern @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $service->slug) }}"
                               placeholder="Bỏ trống để tự sinh">
                    </div>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Giá & Thời gian --}}
            <div class="form-card">
                <h3 class="section-title">
                    <i class="fas fa-money-bill-wave mr-2"></i>Giá & Thời gian
                </h3>

                <div class="row">
                    {{-- Giá gốc --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-money-bill mr-1"></i>Giá gốc (VND)
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-money-bill"></i>
                                <input type="number" 
                                       name="price_original" 
                                       id="price_original"
                                       min="0" 
                                       step="1"
                                       class="form-control form-control-modern @error('price_original') is-invalid @enderror"
                                       value="{{ old('price_original', $service->price_original) }}"
                                       oninput="calculatePrice()">
                            </div>
                            @error('price_original')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Giá khuyến mãi --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-percent mr-1"></i>Giá khuyến mãi (VND)
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-percent"></i>
                                <input type="number" 
                                       name="price_sale" 
                                       id="price_sale"
                                       min="0" 
                                       step="1"
                                       class="form-control form-control-modern @error('price_sale') is-invalid @enderror"
                                       value="{{ old('price_sale', $service->price_sale) }}"
                                       placeholder="Để trống nếu không giảm"
                                       oninput="calculatePrice()">
                            </div>
                            @error('price_sale')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Giá (cũ) --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-coins mr-1"></i>Giá (thừa kế cũ)
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-coins"></i>
                                <input type="number" 
                                       name="price" 
                                       min="0" 
                                       step="0.01"
                                       class="form-control form-control-modern @error('price') is-invalid @enderror"
                                       value="{{ old('price', $service->price) }}">
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Thời lượng --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-stopwatch mr-1"></i>Thời lượng (phút) <span class="required-mark">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-stopwatch"></i>
                                <input type="number" 
                                       name="duration" 
                                       id="duration" 
                                       min="1" 
                                       required
                                       class="form-control form-control-modern @error('duration') is-invalid @enderror"
                                       value="{{ old('duration', $service->duration) }}"
                                       oninput="updateDurationPreview()">
                            </div>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Duration Preview --}}
                            <div class="duration-preview">
                                <div class="text-center">
                                    <h6 style="color: #1e40af; margin-bottom: 8px;">
                                        <i class="fas fa-clock mr-2"></i>Thời lượng ước tính
                                    </h6>
                                    <div id="durationDisplay">
                                        <strong id="durationMinutes">{{ $service->duration }}</strong> phút
                                        <span id="durationHours">
                                            @php 
                                                $h = floor($service->duration / 60);
                                                $m = $service->duration % 60;
                                            @endphp
                                            ({{ $h > 0 ? $h . ' giờ ' . $m . ' phút' : $m . ' phút' }})
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Trạng thái & Nổi bật --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-toggle-on mr-1"></i>Trạng thái <span class="required-mark">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-circle"></i>
                                <select name="status" 
                                        class="form-control form-control-modern @error('status') is-invalid @enderror" 
                                        required>
                                    <option value="1" {{ old('status', $service->status) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="0" {{ old('status', $service->status) == 0 ? 'selected' : '' }}>Tạm ngưng</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Checkbox nổi bật --}}
                        <div class="form-check mt-4 p-3 bg-light rounded">
                            <input type="checkbox" 
                                   id="is_featured" 
                                   name="is_featured" 
                                   value="1"
                                   class="form-check-input" 
                                   style="transform: scale(1.2);"
                                   {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label ml-2" for="is_featured" style="font-weight: 600; color: #1e40af;">
                                <i class="fas fa-star mr-1"></i>Đánh dấu dịch vụ nổi bật
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Price Preview --}}
                <div class="price-preview">
                    <div class="text-center">
                        <h5 style="color: #1e40af; margin-bottom: 10px;">
                            <i class="fas fa-calculator mr-2"></i>Xem trước giá bán
                        </h5>
                        <div id="priceDisplay">
                            @php
                                $original = old('price_original', $service->price_original) ?? 0;
                                $sale = old('price_sale', $service->price_sale) ?? 0;
                            @endphp
                            @if($sale > 0 && $sale < $original)
                                <span class="price-original" id="originalPrice">{{ number_format($original) }}đ</span>
                                <br>
                                <span class="price-final" id="finalPrice">{{ number_format($sale) }}đ</span>
                            @else
                                <span class="price-final" id="finalPriceOnly">{{ number_format($original) }}đ</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mô tả & Hình ảnh --}}
            <div class="form-card">
                <h3 class="section-title">
                    <i class="fas fa-images mr-2"></i>Mô tả & Hình ảnh
                </h3>

                {{-- Mô tả chi tiết --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-paragraph mr-1"></i>Mô tả dịch vụ
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-file-alt" style="top: 20px;"></i>
                        <textarea name="description" 
                                  rows="6" 
                                  class="form-control form-control-modern @error('description') is-invalid @enderror"
                                  placeholder="Nhập mô tả chi tiết về dịch vụ...">{{ old('description', $service->description) }}</textarea>
                    </div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    {{-- Ảnh đại diện --}}
           <div class="col-lg-6">
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-image mr-1"></i>Ảnh đại diện
        </label>

    @php
    // Luôn đảm bảo $images là Collection
    $images = $service->images ?? collect();

    // Lấy thumbnail hoặc ảnh đầu tiên
    $currentThumbnail = $service->thumbnail 
        ?? optional($images->first())->image_url;

    $thumbnailUrl = $currentThumbnail ? src_img_get($currentThumbnail) : null;
@endphp


        {{-- Hiển thị ảnh hiện tại nếu có --}}
        @if($thumbnailUrl)
            <div class="text-center mb-3">
                <img src="{{ $thumbnailUrl }}"
                     class="img-fluid rounded shadow-sm"
                     style="max-width: 100%; height: 200px; object-fit: cover; border: 2px solid #e2e8f0;"
                     alt="Ảnh đại diện hiện tại">
                <p class="text-success font-weight-bold mt-2 mb-0">
                    <i class="fas fa-check-circle mr-1"></i>Ảnh đại diện hiện tại
                </p>
            </div>
        @endif

        {{-- Khu vực upload + preview --}}
        <div class="file-upload-area border border-dashed border-primary rounded p-4 text-center position-relative"
             onclick="document.getElementById('thumbnailUpload').click()"
             style="cursor: pointer; background: #f8faff; min-height: 180px;">
             
            <div id="thumbnailUploadContent" @if($thumbnailUrl && !request()->hasFile('thumbnail')) style="display: none;" @endif>
                <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color: #3b82f6;"></i>
                <p class="mb-1" style="color: #1e40af; font-weight: 600;">Nhấp để chọn ảnh mới</p>
                <small class="text-muted d-block">JPG, PNG, WEBP, GIF • Tối đa 5MB</small>
            </div>

            <div id="thumbnailPreview" style="display: none;">
                <img id="thumbnailPreviewImg" 
                     class="rounded shadow-sm"
                     style="max-width: 100%; max-height: 200px; object-fit: cover; border: 2px solid #10b981;">
                <p class="mt-3 mb-0 text-success font-weight-bold">
                    <i class="fas fa-check-circle mr-1"></i>Đã chọn ảnh mới
                </p>
            </div>
        </div>

        <input type="file"
               name="thumbnail"
               id="thumbnailUpload"
               class="form-control @error('thumbnail') is-invalid @enderror"
               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
               style="display: none;"
               onchange="previewThumbnail(this)">

        @error('thumbnail')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

                    {{-- Ảnh phụ (gallery) --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-images mr-1"></i>Ảnh chi tiết (tối đa 6 ảnh)
                            </label>

                            {{-- Hiển thị ảnh hiện có --}}
                            <div class="row g-2 mb-3">
                                @if($service->images && $service->images->count() > 0)
                                    @foreach($service->images as $image)
                                        <div class="col-4 position-relative">
                                            <img src="{{ src_img_get($image->image_url) }}" 
                                                 class="image-thumbnail"
                                                 alt="Gallery Image">
                                            
                                            <div class="delete-checkbox">
                                                <input type="checkbox" 
                                                       name="delete_images[]" 
                                                       value="{{ $image->id }}"
                                                       class="form-check-input" 
                                                       id="delete_image_{{ $image->id }}">
                                                <label class="form-check-label" 
                                                       for="delete_image_{{ $image->id }}" 
                                                       title="Xóa ảnh">
                                                    <i class="fas fa-times text-danger"></i>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <div class="text-muted text-center py-4 border rounded">
                                            <i class="fas fa-images fa-3x mb-3 opacity-25"></i>
                                            <p>Chưa có ảnh chi tiết nào</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Upload area cho gallery --}}
                            <div class="file-upload-area" onclick="document.getElementById('galleryUpload').click()">
                                <div id="galleryUploadContent">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color: #3b82f6;"></i>
                                    <p class="mb-1" style="color: #1e40af; font-weight: 600;">Nhấp để chọn nhiều ảnh</p>
                                    <small class="text-muted">Hỗ trợ: JPG, PNG, WEBP, GIF (Max: 5MB/ảnh)</small>
                                </div>
                                <div id="galleryPreview" class="gallery-preview"></div>
                            </div>

                            <input type="file" 
                                   name="gallery[]" 
                                   id="galleryUpload"
                                   multiple
                                   class="form-control @error('gallery') is-invalid @enderror" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   style="display: none;"
                                   onchange="previewGallery(this)">

                            @error('gallery.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="button-group">
                <button type="submit" class="btn btn-save">
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
<script>
    // Add Font Awesome if not already included
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
        document.head.appendChild(link);
    }

    // Calculate price preview
    function calculatePrice() {
        const priceOriginal = parseFloat(document.getElementById('price_original').value) || 0;
        const priceSale = parseFloat(document.getElementById('price_sale').value) || 0;
        const priceDisplay = document.getElementById('priceDisplay');
        
        let html = '';
        
        if (priceSale > 0 && priceSale < priceOriginal) {
            html = `
                <span class="price-original" id="originalPrice">${formatPrice(priceOriginal)}</span>
                <br>
                <span class="price-final" id="finalPrice">${formatPrice(priceSale)}</span>
            `;
        } else {
            html = `<span class="price-final" id="finalPriceOnly">${formatPrice(priceOriginal)}</span>`;
        }
        
        priceDisplay.innerHTML = html;
    }

    // Update duration preview
    function updateDurationPreview() {
        const duration = parseInt(document.getElementById('duration').value) || 0;
        const hours = Math.floor(duration / 60);
        const minutes = duration % 60;
        
        document.getElementById('durationMinutes').textContent = duration;
        
        let durationText = '';
        if (hours > 0) {
            durationText = `${hours} giờ ${minutes} phút`;
        } else {
            durationText = `${minutes} phút`;
        }
        
        document.getElementById('durationHours').textContent = `(${durationText})`;
    }

    // Format price to Vietnamese currency
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    }

    // Preview thumbnail image
    function previewThumbnail(input) {
        const uploadContent = document.getElementById('thumbnailUploadContent');
        const previewDiv = document.getElementById('thumbnailPreview');
        const previewImg = document.getElementById('thumbnailPreviewImg');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                uploadContent.style.display = 'none';
                previewDiv.style.display = 'block';
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Preview gallery images
    function previewGallery(input) {
        const previewDiv = document.getElementById('galleryPreview');
        const uploadContent = document.getElementById('galleryUploadContent');
        previewDiv.innerHTML = '';
        
        if (input.files) {
            uploadContent.style.display = 'none';
            
            [...input.files].forEach(file => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'position-relative';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-thumbnail';
                    img.style.width = '100px';
                    img.style.height = '100px';
                    
                    imgContainer.appendChild(img);
                    previewDiv.appendChild(imgContainer);
                };
                
                reader.readAsDataURL(file);
            });
        }
    }

    // File drag and drop functionality
    const fileUploadAreas = document.querySelectorAll('.file-upload-area');
    
    fileUploadAreas.forEach(area => {
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        area.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            const input = this.parentElement.querySelector('input[type="file"]');
            
            if (files.length > 0) {
                if (input.id === 'thumbnailUpload') {
                    input.files = files;
                    previewThumbnail(input);
                } else if (input.id === 'galleryUpload') {
                    input.files = files;
                    previewGallery(input);
                }
            }
        });
    });

    // Form validation
    document.getElementById('serviceForm').addEventListener('submit', function(e) {
        const requiredFields = ['service_name', 'category_id', 'type', 'duration', 'status'];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculatePrice();
        updateDurationPreview();
    });
</script>
<script>
function previewThumbnail(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const preview = document.getElementById('thumbnailPreview');
        const uploadContent = document.getElementById('thumbnailUploadContent');
        const previewImg = document.getElementById('thumbnailPreviewImg');

        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            uploadContent.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Nếu có ảnh cũ → ẩn nút upload, hiện preview (khi load trang)
document.addEventListener('DOMContentLoaded', function() {
    const hasCurrentImage = {!! $thumbnailUrl ? 'true' : 'false' !!};
    const preview = document.getElementById('thumbnailPreview');
    const uploadContent = document.getElementById('thumbnailUploadContent');

    if (hasCurrentImage && !document.querySelector('#thumbnailUpload').files.length) {
        uploadContent.style.display = 'none';
    }
});
</script>
@endpush
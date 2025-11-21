@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị sản phẩm')
@section('page-title', 'Chỉnh sửa sản phẩm')

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
        display: none;
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="create-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size: 2.5rem; font-weight: 700;">
                    <i class="fas fa-edit mr-3"></i>Chỉnh sửa sản phẩm
                </h1>
                <p class="mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                    Cập nhật thông tin sản phẩm trong hệ thống
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('admin.products.index') }}" class="btn btn-cancel">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="form-container">
        <form action="{{ route('admin.products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            @method('PUT')

            {{-- Thông tin cơ bản --}}
            <div class="form-card">
                <h3 class="section-title">
                    <i class="fas fa-info-circle mr-2"></i>Thông tin cơ bản
                </h3>

                {{-- Tên sản phẩm --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-tag mr-1"></i>Tên sản phẩm <span class="required-mark">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-box"></i>
                        <input type="text" 
                               name="product_name" 
                               class="form-control form-control-modern @error('product_name') is-invalid @enderror" 
                               value="{{ old('product_name', $product->product_name) }}" 
                               placeholder="Nhập tên sản phẩm..."
                               required>
                    </div>
                    @error('product_name')
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
                                            {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>
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

                    {{-- Thương hiệu --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-trademark mr-1"></i>Thương hiệu
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-building"></i>
                                <select name="brand_id" class="form-control form-control-modern @error('brand_id') is-invalid @enderror">
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->brand_id }}" 
                                            {{ old('brand_id', $product->brand_id) == $brand->brand_id ? 'selected' : '' }}>
                                            {{ $brand->brand_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('brand_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Dung tích --}}
                    <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                        <i class="fas fa-flask mr-1"></i>Dung tích
                        <small class="text-gray">(ví dụ: 140ml, 500ml, 2x500ml)</small>
                        </label>
                        <div class="input-icon">
                        <i class="fas fa-beaker"></i>
                        <input type="text"
                                name="capacity"
                                class="form-control form-control-modern @error('capacity') is-invalid @enderror"
                                value="{{ old('capacity', $product->capacity ?? '') }}"
                                placeholder="500ml">
                        </div>
                        @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Quick-fill gợi ý --}}
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                        @foreach(['180ml','500ml','200ml','50ml'] as $cap)
                            <button type="button" class="btn btn-sm btn-outline-primary text-white"
                                    onclick="document.querySelector('[name=capacity]').value='{{ $cap }}'">{{ $cap }}</button>
                        @endforeach
                        </div>
                    </div>
                    </div>

                </div>
            </div>

            {{-- Thông tin giá và kho --}}
            <div class="form-card">
                <h3 class="section-title">
                    <i class="fas fa-money-bill-wave mr-2"></i>Thông tin giá và kho hàng
                </h3>

                <div class="row">
                    {{-- Giá bán --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-dollar-sign mr-1"></i>Giá bán <span class="required-mark">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-money-bill"></i>
                                <input type="number" 
                                       name="price" 
                                       id="price"
                                       class="form-control form-control-modern @error('price') is-invalid @enderror" 
                                       value="{{ old('price', $product->price) }}" 
                                       min="0" 
                                       placeholder="0"
                                       oninput="calculateFinalPrice()"
                                       required>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Giảm giá --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-percentage mr-1"></i>Giảm giá (%)
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-percent"></i>
                                <input type="number" 
                                       name="discount_percent" 
                                       id="discount_percent"
                                       class="form-control form-control-modern @error('discount_percent') is-invalid @enderror" 
                                       value="{{ old('discount_percent', $product->discount_percent) }}" 
                                       min="0" 
                                       max="100" 
                                       placeholder="0"
                                       oninput="calculateFinalPrice()">
                            </div>
                            @error('discount_percent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Số lượng tồn --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-cubes mr-1"></i>Số lượng tồn <span class="required-mark">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-warehouse"></i>
                                <input type="number" 
                                       name="stock_quantity" 
                                       class="form-control form-control-modern @error('stock_quantity') is-invalid @enderror" 
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                                       min="0" 
                                       placeholder="0"
                                       required>
                            </div>
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Price Preview --}}
                <div class="price-preview" id="pricePreview">
                    <div class="text-center">
                        <h5 style="color: #1e40af; margin-bottom: 10px;">
                            <i class="fas fa-calculator mr-2"></i>Xem trước giá bán
                        </h5>
                        <div id="priceDisplay">
                            <span class="price-original" id="originalPrice">0đ</span>
                            <br>
                            <span class="price-final" id="finalPrice">0đ</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cấu hình bổ sung --}}
            <div class="form-card">
                <h3 class="section-title">
                    <i class="fas fa-cog mr-2"></i>Cấu hình bổ sung
                </h3>

                <div class="row">
                    {{-- Trạng thái --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-toggle-on mr-1"></i>Trạng thái <span class="required-mark">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-circle"></i>
                                <select name="status" class="form-control form-control-modern @error('status') is-invalid @enderror" required>
                                    <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>Đang bán</option>
                                    <option value="2" {{ old('status', $product->status) == 2 ? 'selected' : '' }}>Ngưng bán</option>
                                    <option value="3" {{ old('status', $product->status) == 3 ? 'selected' : '' }}>Hết hàng</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Hình ảnh chính --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-image mr-1"></i>Ảnh chính
                            </label>

                            @php 
                                $mainImage = is_array($product->images) && count($product->images) 
                                    ? $product->images[0] 
                                    : null; 
                            @endphp

                            @if($mainImage)
                                <div class="text-center mb-3">
                                    <img src="{{ asset($mainImage) }}" class="current-image" alt="{{ $product->product_name }}">
                                    <p class="text-success font-weight-bold">
                                        <i class="fas fa-check-circle mr-1"></i>Ảnh hiện tại
                                    </p>
                                </div>
                            @endif

                            <div class="file-upload-area" onclick="document.getElementById('images').click()">
                                <div id="uploadContent">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color: #3b82f6;"></i>
                                    <p class="mb-1" style="color: #1e40af; font-weight: 600;">Nhấp để chọn ảnh mới</p>
                                    <small class="text-muted">Hỗ trợ: JPG, PNG, WEBP (Max: 2MB)</small>
                                </div>
                                <div id="imagePreview" style="display: none;">
                                    <img id="previewImg" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                                    <p class="mt-2 mb-0 text-success font-weight-bold">
                                        <i class="fas fa-check-circle mr-1"></i>Ảnh đã chọn
                                    </p>
                                </div>
                            </div>

                            <input type="file" 
                                name="images" 
                                id="images"
                                class="form-control @error('images') is-invalid @enderror" 
                                accept="image/*"
                                style="display: none;"
                                onchange="previewImage(this)">

                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                {{-- Ảnh phụ --}}
<div class="col-md-12">
  <div class="form-group">
    <label class="form-label">
      <i class="fas fa-images mr-1"></i>Ảnh phụ
    </label>

    {{-- Hiển thị ảnh phụ hiện có --}}
    <div class="d-flex flex-wrap gap-3 mb-3">
      @if($product->imagesRel && $product->imagesRel->count())
        @foreach($product->imagesRel as $img)
          @if(!$img->is_main) 
            <div class="text-center" style="position: relative;">
              <img src="{{ asset($img->url) }}" 
                   style="max-width: 120px; border-radius: 6px; border:1px solid #ddd;">

              {{-- Checkbox xoá --}}
              <label class="d-block mt-1">
                <input type="checkbox" name="delete_sub_images[]" value="{{ $img->id }}">
                Xóa
              </label>
            </div>
          @endif
        @endforeach
      @else
        <p class="text-muted">Chưa có ảnh phụ</p>
      @endif
    </div>

    {{-- Upload ảnh phụ mới --}}
    <div class="file-upload-area" onclick="document.getElementById('gallery').click()">
      <div>
        <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color: #3b82f6;"></i>
        <p class="mb-1" style="color: #1e40af; font-weight: 600;">Nhấp để chọn nhiều ảnh mới</p>
        <small class="text-muted">Hỗ trợ: JPG, PNG, WEBP (Max: 2MB/ảnh)</small>
      </div>
      <div id="galleryPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
    </div>

    <input type="file" 
           name="gallery[]" 
           id="gallery"
           multiple
           class="form-control @error('gallery') is-invalid @enderror" 
           accept="image/*"
           style="display: none;"
           onchange="previewGallery(this)">

    @error('gallery')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>




                </div>

                {{-- Mô tả sản phẩm --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left mr-1"></i>Mô tả sản phẩm
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-file-alt" style="top: 20px;"></i>
                        <textarea name="description" 
                                  class="form-control form-control-modern @error('description') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="Nhập mô tả chi tiết về sản phẩm...">{{ old('description', $product->description) }}</textarea>
                    </div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="button-group">
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save mr-2"></i>Cập nhật
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-cancel">
                    <i class="fas fa-times mr-2"></i>Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Add Font Awesome if not already included
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
        document.head.appendChild(link);
    }

    // Calculate final price with discount
    function calculateFinalPrice() {
        const price = parseFloat(document.getElementById('price').value) || 0;
        const discount = parseFloat(document.getElementById('discount_percent').value) || 0;
        
        const finalPrice = price - (price * discount / 100);
        const pricePreview = document.getElementById('pricePreview');
        
        if (price > 0) {
            pricePreview.style.display = 'block';
            document.getElementById('originalPrice').textContent = formatPrice(price);
            document.getElementById('finalPrice').textContent = formatPrice(finalPrice);
            
            if (discount > 0) {
                document.getElementById('originalPrice').style.display = 'inline';
            } else {
                document.getElementById('originalPrice').style.display = 'none';
            }
        } else {
            pricePreview.style.display = 'none';
        }
    }

    // Format price to Vietnamese currency
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    }

    // Preview uploaded image
    function previewImage(input) {
        const uploadContent = document.getElementById('uploadContent');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                uploadContent.style.display = 'none';
                imagePreview.style.display = 'block';
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // File drag and drop functionality
    const fileUploadArea = document.querySelector('.file-upload-area');
    
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('images').files = files;
            previewImage(document.getElementById('images'));
        }
    });

    // Form validation
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const requiredFields = ['product_name', 'category_id', 'price', 'stock_quantity', 'status'];
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

    // Calculate price on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculateFinalPrice();
    });
    function previewGallery(input) {
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = "";

    if (input.files) {
        [...input.files].forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement("img");
                img.src = e.target.result;
                img.className = "rounded border";
                img.style.maxWidth = "100px";
                img.style.marginRight = "8px";
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
}

</script>
@endpush
@endsection
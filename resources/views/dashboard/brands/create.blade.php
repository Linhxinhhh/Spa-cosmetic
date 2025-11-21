@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị thương hiệu')
@section('page-title', 'Thêm thương hiệu')

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
    
    .form-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
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
                    <i class="fas fa-plus-circle mr-3"></i>Thêm thương hiệu mới
                </h1>
                <p class="mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                    Điền thông tin chi tiết để thêm thương hiệu vào hệ thống
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('admin.brands.index') }}" class="btn btn-cancel">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="form-container">
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" id="brandForm">
            @csrf

            {{-- Thông tin cơ bản --}}
            <div class="form-card">
                <h3 class="section-title">
                    <i class="fas fa-info-circle mr-2"></i>Thông tin thương hiệu
                </h3>

                {{-- Tên thương hiệu --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-trademark mr-1"></i>Tên thương hiệu <span class="required-mark">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-building"></i>
                        <input type="text" 
                               name="brand_name" 
                               class="form-control form-control-modern @error('brand_name') is-invalid @enderror" 
                               value="{{ old('brand_name') }}" 
                               placeholder="Nhập tên thương hiệu..." 
                               required>
                    </div>
                    @error('brand_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Logo thương hiệu --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-image mr-1"></i>Logo thương hiệu
                    </label>
                    <div class="file-upload-area" onclick="document.getElementById('logo').click()">
                        <div id="uploadContent">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color: #3b82f6;"></i>
                            <p class="mb-1" style="color: #1e40af; font-weight: 600;">Nhấp để chọn logo</p>
                            <small class="text-muted">Hỗ trợ: JPG, PNG, GIF (Max: 2MB)</small>
                        </div>
                        <div id="imagePreview" style="display: none;">
                            <img id="previewImg" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                            <p class="mt-2 mb-0 text-success font-weight-bold">
                                <i class="fas fa-check-circle mr-1"></i>Logo đã chọn
                            </p>
                        </div>
                    </div>
                    <input type="file" 
                           name="logo" 
                           id="logo"
                           class="form-control @error('logo') is-invalid @enderror" 
                           accept="image/*"
                           style="display: none;"
                           onchange="previewImage(this)">
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mô tả thương hiệu --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left mr-1"></i>Mô tả thương hiệu
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-file-alt" style="top: 20px;"></i>
                        <textarea name="description" 
                                  class="form-control form-control-modern @error('description') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="Nhập mô tả chi tiết về thương hiệu...">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Trạng thái --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-toggle-on mr-1"></i>Trạng thái <span class="required-mark">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-circle"></i>
                        <select name="status" class="form-control form-control-modern @error('status') is-invalid @enderror" required>
                             <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Ngưng bán</option>
                            <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Đang bán</option>
                           
                             <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>Hết hàng</option>
                        </select>
                    </div>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="button-group">
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save mr-2"></i>Lưu thương hiệu
                </button>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-cancel">
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

    // Preview uploaded logo
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
            document.getElementById('logo').files = files;
            previewImage(document.getElementById('logo'));
        }
    });

    // Form validation
    document.getElementById('brandForm').addEventListener('submit', function(e) {
        const requiredFields = ['brand_name', 'status'];
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
</script>
@endpush
@endsection
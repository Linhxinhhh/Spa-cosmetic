@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị nhân viên')
@section('page-title', 'Chỉnh sửa nhân viên')

@push('styles')
<link href="{{ asset('admin/giaodien/css/style.css') }}" rel="stylesheet">

@endpush

@section('content')
<div class="staff-edit-container">
  {{-- Header --}}
  <div class="service-header">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1>
          <i class="fas fa-user-edit mr-2"></i>Chỉnh sửa nhân viên
        </h1>
        <p>Cập nhật thông tin nhân viên trong hệ thống</p>
        <div class="service-id-badge">
          <i class="fas fa-hashtag mr-1"></i>ID: {{ $staff->user_id }}
        </div>
      </div>
      <div class="col-md-4 text-right">
        <a href="{{ route('admin.staffs.index') }}" class="btn-cancel">
          <i class="fas fa-arrow-left"></i>
          <span>Quay lại</span>
        </a>
      </div>
    </div>
  </div>

  {{-- Errors --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <strong><i class="fas fa-exclamation-triangle mr-2"></i>Có lỗi xảy ra:</strong>
      <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form --}}
  <div class="form-container">
    <form id="staffEditForm"
          action="{{ route('admin.staffs.update', $staff) }}"
          method="POST"
          enctype="multipart/form-data">
      @csrf 
      @method('PUT')

      {{-- Thông tin cơ bản --}}
      <div class="form-card">
        <h3 class="section-title">
          <i class="fas fa-info-circle mr-2"></i>Thông tin cơ bản
        </h3>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-user-tag mr-1"></i>Họ tên <span class="required-mark">*</span>
          </label>
          <div class="input-icon">
            <i class="fas fa-user"></i>
            <input type="text" name="name"
                   class="form-control form-control-modern @error('name') is-invalid @enderror"
                   value="{{ old('name', optional($staff->user)->name ?? $staff->name ?? '') }}" 
                   placeholder="Nhập họ tên nhân viên"
                   required>
          </div>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">
                <i class="fas fa-envelope mr-1"></i>Email <span class="required-mark">*</span>
              </label>
              <div class="input-icon">
                <i class="fas fa-at"></i>
                <input type="email" name="email"
                       class="form-control form-control-modern @error('email') is-invalid @enderror"
                       value="{{ old('email', $staff->email ?? '') }}" 
                       placeholder="example@email.com"
                       required>
              </div>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">
                <i class="fas fa-phone mr-1"></i>Số điện thoại <span class="required-mark">*</span>
              </label>
              <div class="input-icon">
                <i class="fas fa-mobile-alt"></i>
                <input type="tel" name="phone"
                       class="form-control form-control-modern @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $staff->phone ?? '') }}" 
                       placeholder="0xxxxxxxxx"
                       required>
              </div>
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-map-marker-alt mr-1"></i>Địa chỉ
          </label>
          <div class="input-icon">
            <i class="fas fa-home"></i>
            <input type="text" name="address"
                   class="form-control form-control-modern @error('address') is-invalid @enderror"
                   value="{{ old('address', $staff->address ?? '') }}"
                   placeholder="Nhập địa chỉ">
          </div>
          @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      

      

      {{-- Button Group --}}
      <div class="button-group">
        <a href="{{ route('admin.staffs.index') }}" class="btn-cancel">
          <i class="fas fa-times"></i>
          <span>Hủy bỏ</span>
        </a>
        <button class="btn-update" type="submit">
          <i class="fas fa-save"></i>
          <span>Cập nhật nhân viên</span>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/giaodien/js/main.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format số điện thoại
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '').slice(0, 10);
            e.target.value = value;
        });
    }

    // Avatar upload preview
    const avatarInput = document.getElementById('avatarInput');
    const uploadArea = document.getElementById('uploadArea');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const removePreview = document.getElementById('removePreview');
    const currentAvatar = document.getElementById('currentAvatar');

    // Click to upload
    avatarInput.addEventListener('change', function(e) {
        handleFile(e.target.files[0]);
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#667eea';
        uploadArea.style.background = '#f8f9fa';
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#cbd5e0';
        uploadArea.style.background = 'white';
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#cbd5e0';
        uploadArea.style.background = 'white';
        
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            handleFile(file);
        }
    });

    function handleFile(file) {
        if (!file || !file.type.startsWith('image/')) {
            alert('Vui lòng chọn file ảnh!');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Kích thước ảnh không được vượt quá 2MB!');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            previewContainer.style.display = 'block';
            uploadArea.style.display = 'none';
            currentAvatar.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    // Remove preview
    removePreview.addEventListener('click', function() {
        avatarInput.value = '';
        previewContainer.style.display = 'none';
        uploadArea.style.display = 'block';
        currentAvatar.style.display = 'block';
        imagePreview.src = '';
    });

    // Form validation
    const form = document.getElementById('staffEditForm');
    form.addEventListener('submit', function(e) {
        const requiredInputs = form.querySelectorAll('[required]');
        let isValid = true;

        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
        }
    });
});
</script>
@endpush
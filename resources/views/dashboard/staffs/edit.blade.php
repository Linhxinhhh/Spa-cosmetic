@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị nhân viên')
@section('page-title', 'Chỉnh sửa nhân viên')

@push('styles')
<link href="{{ asset('admin/giaodien/css/style.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
  {{-- Header --}}
  <div class="edit-header">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="mb-2" style="font-weight:700">
          <i class="fas fa-user-edit mr-2"></i>Chỉnh sửa nhân viên
        </h1>
        <p class="mb-2" style="opacity:.9">Cập nhật thông tin nhân viên trong hệ thống</p>
        <div class="service-id-badge">
          <i class="fas fa-hashtag mr-1"></i>ID: {{ $staff->id }}
        </div>
      </div>
      <div class="col-md-4 text-right">
        <a href="{{ route('admin.staffs.index') }}" class="btn btn-cancel">
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
    <form id="staffEditForm"
          action="{{ route('admin.staffs.update', $staff->staff_id) }}"
          method="POST">
      @csrf @method('PUT')

      {{-- Thông tin cơ bản --}}
      <div class="form-card">
        <h3 class="section-title"><i class="fas fa-info-circle mr-2"></i>Thông tin cơ bản</h3>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-user-tag mr-1"></i>Họ và tên <span class="required-mark">*</span>
          </label>
          <div class="input-icon">
            <i class="fas fa-user"></i>
            {{-- Fix: Sử dụng optional() để tránh null error --}}
            <input type="text" name="name"
                   class="form-control form-control-modern @error('name') is-invalid @enderror"
                   value="{{ old('name', optional($staff->user)->name ?? $staff->full_name ?? '') }}" required>
          </div>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label"><i class="fas fa-envelope mr-1"></i>Email <span class="required-mark">*</span></label>
              <div class="input-icon">
                <i class="fas fa-at"></i>
                <input type="email" name="email"
                       class="form-control form-control-modern @error('email') is-invalid @enderror"
                       value="{{ old('email', $staff->email ?? '') }}" required>
              </div>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label"><i class="fas fa-map-marker-alt mr-1"></i>Địa chỉ</label>
              <div class="input-icon">
                <i class="fas fa-home"></i>
                <input type="text" name="address"
                       class="form-control form-control-modern @error('address') is-invalid @enderror"
                       value="{{ old('address', $staff->address ?? '') }}">
              </div>
              @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Thông tin liên hệ & lương --}}
      <div class="form-card">
        <h3 class="section-title"><i class="fas fa-phone mr-2"></i>Thông tin liên hệ & lương</h3>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label"><i class="fas fa-phone mr-1"></i>Số điện thoại <span class="required-mark">*</span></label>
              <div class="input-icon">
                <i class="fas fa-mobile-alt"></i>
                <input type="tel" name="phone"
                       class="form-control form-control-modern @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $staff->phone ?? '') }}" required>
              </div>
              @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

        </div>
      </div>

      <div class="button-group">
        <button class="btn btn-update" type="submit">
          <i class="fas fa-save mr-2"></i>Cập nhật nhân viên
        </button>
        <a href="{{ route('admin.staffs.index') }}" class="btn btn-cancel">
          <i class="fas fa-times mr-2"></i>Hủy bỏ
        </a>
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
    });
</script>
@endpush
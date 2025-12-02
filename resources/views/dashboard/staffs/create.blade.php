@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị nhân viên')
@section('page-title', 'Thêm nhân viên')

@push('styles')
<link href="{{ asset('admin/giaodien/css/style.css') }}" rel="stylesheet">
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
                    <i class="fas fa-user-plus mr-3"></i>Thêm nhân viên mới
                </h1>
                <p class="mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                    Điền thông tin chi tiết để thêm nhân viên vào hệ thống
                </p>
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
        <form id="staffForm" action="{{ route('admin.staffs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Thông tin cơ bản --}}
            <div class="form-card">
                <h3 class="section-title"><i class="fas fa-info-circle mr-2"></i>Thông tin cơ bản</h3>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-tag mr-1"></i>Họ và tên <span class="required-mark">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="full_name"
                               class="form-control form-control-modern @error('full_name') is-invalid @enderror"
                               value="{{ old('full_name') }}" required>
                    </div>
                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-envelope mr-1"></i>Email đăng nhập <span class="required-mark">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-at"></i>
                                <input type="email" name="email"
                                       class="form-control form-control-modern @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                            </div>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-lock mr-1"></i>Mật khẩu <span class="required-mark">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-key"></i>
                                <input type="password" name="password"
                                       class="form-control form-control-modern @error('password') is-invalid @enderror"
                                       value="{{ old('password') }}" required>
                            </div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-phone mr-1"></i>Số điện thoại <span class="required-mark">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-mobile-alt"></i>
                                <input type="tel" name="phone"
                                       class="form-control form-control-modern @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}" required>
                            </div>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-map-marker-alt mr-1"></i>Địa chỉ</label>
                            <div class="input-icon">
                                <i class="fas fa-home"></i>
                                <input type="text" name="address"
                                       class="form-control form-control-modern @error('address') is-invalid @enderror"
                                       value="{{ old('address') }}">
                            </div>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="button-group">
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save mr-2"></i>Lưu nhân viên
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
@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị nhân viên')
@section('page-title', 'Thêm nhân viên')

@push('styles')
<link href="{{ asset('admin/giaodien/css/style.css') }}" rel="stylesheet">

@endpush

@section('content')
<div class="staff-create-container">

    {{-- Header --}}
    <div class="service-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>
                    <i class="fas fa-user-plus"></i>Thêm nhân viên mới
                </h1>
                <p>Điền thông tin để thêm nhân viên vào hệ thống</p>
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
            <ul class="mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Flash error --}}
    @if (session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Form --}}
    <div class="form-container">
        <form id="staffForm" action="{{ route('admin.staffs.store') }}" method="POST">
            @csrf

            <div class="form-card">
                <h3 class="section-title">
                    <i class="fas fa-info-circle mr-2"></i>Thông tin cơ bản
                </h3>

                {{-- Name --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-tag mr-1"></i>Họ tên <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="Nhập họ tên nhân viên"
                            required>
                    </div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email + Phone --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope mr-1"></i>Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-at"></i>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
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
                                <i class="fas fa-phone mr-1"></i>Số điện thoại <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-mobile-alt"></i>
                                <input type="tel" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}"
                                    placeholder="0xxxxxxxxx"
                                    required>
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt mr-1"></i>Địa chỉ
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-home"></i>
                        <input type="text" name="address"
                            class="form-control"
                            value="{{ old('address') }}"
                            placeholder="Nhập địa chỉ">
                    </div>
                </div>
            </div>

            {{-- Security Card --}}
            <div class="form-card">
                <h3 class="section-title">
                    <i class="fas fa-lock mr-2"></i>Thông tin bảo mật
                </h3>

                {{-- Password --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-key mr-1"></i>Mật khẩu <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon" style="position: relative;">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="password"
                                    class="form-control"
                                    placeholder="Tối thiểu 8 ký tự"
                                    required>
                                <button type="button" class="password-toggle" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength">
                                <div class="password-strength-bar" id="passwordStrengthBar"></div>
                            </div>
                            <div class="password-hint">
                                Mật khẩu nên có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số
                            </div>
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-key mr-1"></i>Xác nhận mật khẩu <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon" style="position: relative;">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password_confirmation" id="passwordConfirm"
                                    class="form-control"
                                    placeholder="Nhập lại mật khẩu"
                                    required>
                                <button type="button" class="password-toggle" id="togglePasswordConfirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="passwordMatchMessage" style="font-size: 0.85rem; margin-top: 8px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="button-group">
                <a href="{{ route('admin.staffs.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>Hủy bỏ</span>
                </a>
                <button type="submit" class="btn-update">
                    <i class="fas fa-save"></i>
                    <span>Lưu nhân viên</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Phone format
    const phone = document.querySelector('input[name="phone"]');
    if (phone) {
        phone.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
        });
    }

    // Password toggle
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirm = document.getElementById('passwordConfirm');

    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }

    if (togglePasswordConfirm) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirm.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }

    // Password strength
    const passwordStrengthBar = document.getElementById('passwordStrengthBar');
    if (password && passwordStrengthBar) {
        password.addEventListener('input', function() {
            const value = this.value;
            let strength = 0;

            if (value.length >= 8) strength++;
            if (/[a-z]/.test(value) && /[A-Z]/.test(value)) strength++;
            if (/[0-9]/.test(value)) strength++;

            passwordStrengthBar.className = 'password-strength-bar';
            if (strength === 1) passwordStrengthBar.classList.add('weak');
            else if (strength === 2) passwordStrengthBar.classList.add('medium');
            else if (strength === 3) passwordStrengthBar.classList.add('strong');
        });
    }

    // Password match
    const passwordMatchMessage = document.getElementById('passwordMatchMessage');
    if (passwordConfirm && passwordMatchMessage) {
        passwordConfirm.addEventListener('input', function() {
            if (this.value === '') {
                passwordMatchMessage.textContent = '';
                return;
            }

            if (this.value === password.value) {
                passwordMatchMessage.innerHTML = '<i class="fas fa-check-circle mr-1" style="color: #22c55e;"></i><span style="color: #22c55e;">Mật khẩu khớp</span>';
            } else {
                passwordMatchMessage.innerHTML = '<i class="fas fa-times-circle mr-1" style="color: #f44336;"></i><span style="color: #f44336;">Mật khẩu không khớp</span>';
            }
        });
    }

    // Form validation
    const form = document.getElementById('staffForm');
    form.addEventListener('submit', function(e) {
        if (password.value !== passwordConfirm.value) {
            e.preventDefault();
            alert('Mật khẩu xác nhận không khớp!');
            passwordConfirm.focus();
            return false;
        }

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
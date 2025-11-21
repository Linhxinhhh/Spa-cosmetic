@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị khách hàng')
@section('page-title', 'Chỉnh sửa thông tin khách hàng')


@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        min-height: 100vh;
        color: #1e293b;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(10deg); }
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
    }

    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    /* Alerts */
    .alert-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-error {
        background: linear-gradient(135deg, #FF0000, #B91C1C);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Form Container */
    .form-container {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.1);
        margin-bottom: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #1e293b;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control.is-invalid {
        border-color: #FF0000;
    }

    .invalid-feedback {
        color: #FF0000;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    /* Buttons */
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .btn-primary:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }

        .page-title {
            font-size: 2rem;
        }

        .form-control {
            padding: 0.6rem 0.8rem;
            font-size: 0.8rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            font-size: 0.8rem;
        }
    }

    /* Smooth animations */
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container fade-in">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-edit"></i>
            Chỉnh sửa khách hàng
        </h1>
        <p class="page-subtitle">Cập nhật thông tin khách hàng của Lyn & Spa</p>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert-success" id="successAlert">
            <i class="fas fa-check-circle"></i>
            <span id="successMessage">{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="alert-error" id="errorAlert">
            <i class="fas fa-exclamation-circle"></i>
            <span id="errorMessage">{{ $errors->first() }}</span>
        </div>
    @endif

    <!-- Form Container -->
    <div class="form-container">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- USER --}}
    <div class="form-group">
        <label for="name">Họ tên <span style="color:#FF0000">*</span></label>
        <input type="text" name="name" id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $customer->user->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="email">Email <span style="color:#FF0000">*</span></label>
        <input type="email" name="email" id="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $customer->user->email ?? '') }}" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="phone">Số điện thoại <span style="color:#FF0000">*</span></label>
        <input type="tel" name="phone" id="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $customer->user->phone ?? '') }}" required>
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- CUSTOMER --}}
    <div class="form-group">
        <label for="address">Địa chỉ</label>
        <input type="text" name="address" id="address"
               class="form-control @error('address') is-invalid @enderror"
               value="{{ old('address', $customer->address ?? '') }}">
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="birthday">Ngày sinh</label>
        <input type="date" name="birthday" id="birthday"
               class="form-control @error('birthday') is-invalid @enderror"
               value="{{ old('birthday', $customer->birthday?->format('Y-m-d')) }}">
        @error('birthday') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="loyalty_points">Điểm tích lũy</label>
        <input type="number" name="loyalty_points" id="loyalty_points"
               class="form-control @error('loyalty_points') is-invalid @enderror"
               value="{{ old('loyalty_points', $customer->loyalty_points ?? 0) }}">
        @error('loyalty_points') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-primary" id="submitButton">
        <i class="fas fa-save"></i> Cập nhật
    </button>
</form>

    </div>
</div>

@section('scripts')
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 4000);
        }
        
        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.display = 'none';
            }, 4000);
        }

        // Form submission loading state
        const form = document.querySelector('form');
        const submitButton = document.getElementById('submitButton');
        
        form.addEventListener('submit', function(e) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
        });
    });
</script>
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const successAlert = document.getElementById('successAlert');
        const errorAlert   = document.getElementById('errorAlert');
        if (successAlert) setTimeout(()=> successAlert.style.display='none', 4000);
        if (errorAlert)   setTimeout(()=> errorAlert.style.display='none', 4000);

        const form = document.querySelector('form');
        const submitButton = document.getElementById('submitButton');
        form.addEventListener('submit', function() {
          submitButton.disabled = true;
          submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
        });
      });
    </script>
@endsection
@endsection
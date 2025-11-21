@extends('dashboard.layouts.app')

@section('page-title', 'Chỉnh sửa lịch hẹn')

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
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 4px 15px rgba(29, 78, 216, 0.2);
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
        border-color: #1d4ed8;
    }

    .invalid-feedback {
        color: #1d4ed8;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
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

    .btn-secondary {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
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

.field{ position: relative; display:block; width:100%; }

.field .input-icon{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  color:#2563eb;
  pointer-events:none;
  font-size:0.95rem;
  line-height:1;
  width:20px; height:20px;              /* kích thước icon ổn định */
  display:flex; align-items:center; justify-content:center;
}

/* chừa chỗ cho icon bên trái */
.field input[type="date"],
.field input[type="time"]{
  padding-left: 2.5rem;                 /* tăng padding để chữ không dính icon */
  height: 42px;
}

/* đảm bảo phần nút chọn lịch/giờ ở bên phải không đè icon */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator{
  cursor:pointer; opacity:.8; margin-right:2px;
}

.form-hint{ color:#64748b; font-size:.8rem; margin-top:.25rem; }


</style>

<div class="container fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-calendar-edit"></i>
            Chỉnh sửa lịch hẹn
        </h1>
        <p class="page-subtitle">Cập nhật thông tin lịch hẹn của Lyn & Spa</p>
    </div>

    @if(session('success'))
        <div class="alert-success" id="successAlert">
            <i class="fas fa-check-circle"></i>
            <span id="successMessage">{{ session('success') }}</span>
        </div>
    @endif
    {{-- Sửa lỗi: Nếu có $errors->any() thì vòng lặp $errors->all() sẽ liệt kê tất cả --}}
    @if($errors->any())
        <div class="alert-error" id="errorAlert">
            <i class="fas fa-exclamation-circle"></i>
            <span id="errorMessage">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </span>
        </div>
    @endif


    <div class="form-container">
        {{-- SỬA: Dùng $appointment->id thay vì $appointment->appointment_id nếu `id` là khóa chính --}}
        <form action="{{ route('admin.appointments.update', $appointment->appointment_id) }}" method="POST" id="appointmentForm">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="user_id">Khách hàng <span style="color: #1d4ed8;">*</span></label>
                <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                    <option value="">Chọn khách hàng</option>
                    @foreach($users as $user)
                        <option value="{{ $user->user_id }}" {{ old('user_id', $appointment->user_id) == $user->user_id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="service_id">Dịch vụ <span style="color: #1d4ed8;">*</span></label>
                <select name="service_id" id="service_id" class="form-control @error('service_id') is-invalid @enderror" required>
                    <option value="">Chọn dịch vụ</option>
                    @foreach($services as $service)
                        <option value="{{ $service->service_id }}" {{ old('service_id', $appointment->service_id) == $service->service_id ? 'selected' : '' }}>
                            {{ $service->service_name }}
                        </option>
                    @endforeach
                </select>
                @error('service_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
@php
    $apptDate  = old('appointment_date',
                    $appointment->appointment_date
                      ? \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d')
                      : '');

    $startTime = old('start_time',
                    $appointment->start_time ? substr($appointment->start_time, 0, 5) : '');

    $endTime   = old('end_time',
                    $appointment->end_time ? substr($appointment->end_time, 0, 5) : '');
@endphp

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="appointment_date">Ngày hẹn <span style="color:#1d4ed8">*</span></label>
      <div class="field">
        <span class="input-icon"><i class="fas fa-calendar-alt"></i></span>
        <input type="date"
               name="appointment_date" id="appointment_date"
               class="form-control @error('appointment_date') is-invalid @enderror"
               value="{{ $apptDate }}"
               min="{{ now()->toDateString() }}" required>
      </div>
      <div class="form-hint">Chọn ngày theo lịch</div>
      @error('appointment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label for="start_time">Bắt đầu <span style="color:#1d4ed8">*</span></label>
      <div class="field">
        <span class="input-icon"><i class="fas fa-clock"></i></span>
        <input type="time"
               name="start_time" id="start_time"
               class="form-control @error('start_time') is-invalid @enderror"
               value="{{ $startTime }}"
               step="300" required> {{-- bước 5 phút --}}
      </div>
      <div class="form-hint">Định dạng 09:30</div>
      @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label for="end_time">Kết thúc <span style="color:#1d4ed8">*</span></label>
      <div class="field">
        <span class="input-icon"><i class="fas fa-hourglass-end"></i></span>
        <input type="time"
               name="end_time" id="end_time"
               class="form-control @error('end_time') is-invalid @enderror"
               value="{{ $endTime }}"
               step="300" required>
      </div>
      <div class="form-hint">Định dạng 10:30</div>
      @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
</div>
<div class="form-group"> <label for="status">Trạng thái <span style="color: #1d4ed8;">*</span></label> <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required> <option value="pending" {{ old('status', $appointment->status) == 'pending' ? 'selected' : '' }}>Pending</option> <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option> <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option> </select> @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror </div>


            <div class="form-group">
                <label for="notes">Ghi chú</label>
                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="Nhập ghi chú (nếu có)">{{ old('notes', $appointment->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="submitButton">
                    <i class="fas fa-save"></i>
                    Cập nhật
                </button>
                <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </a>
            </div>
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
        
        // Hide after 4 seconds
        const hideAlert = (alertElement) => {
             if (alertElement) {
                setTimeout(() => {
                    alertElement.style.opacity = '0';
                    setTimeout(() => {
                        alertElement.style.display = 'none';
                    }, 500); // Wait for fade-out transition
                }, 4000);
                 alertElement.style.transition = 'opacity 0.5s ease-out';
            }
        };

        hideAlert(successAlert);
        hideAlert(errorAlert);

        // Form submission confirmation and loading state
        const form = document.getElementById('appointmentForm');
        const submitButton = document.getElementById('submitButton');
        
        form.addEventListener('submit', function(e) {
            if (!confirm('Bạn có chắc chắn muốn cập nhật lịch hẹn này?')) {
                e.preventDefault();
                return;
            }
            // Prevent double submission
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
        });
    });
</script>
@endsection
@endsection
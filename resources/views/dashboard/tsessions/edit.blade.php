@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản lý buổi điều trị')
@section('breadcrumb-child', 'Chỉnh sửa buổi')
@section('page-title', 'Chỉnh sửa buổi điều trị')

@push('styles')
<style>
    .edit-session-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .page-header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 32px;
        margin-bottom: 32px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header-card h1 {
        color: white;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }

    .page-header-card .subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        position: relative;
        z-index: 1;
        margin-bottom: 0;
    }

    .session-badge-header {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        color: white;
        font-weight: 600;
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 1;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        margin-bottom: 24px;
    }

    .form-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section-title i {
        color: #667eea;
        font-size: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 14px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-label i {
        color: #94a3b8;
        font-size: 14px;
    }

    .form-label .required {
        color: #ef4444;
        margin-left: 2px;
    }

    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .form-select {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23667eea' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    }

    .input-group-custom {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
        z-index: 10;
    }

    .input-group-custom .form-control,
    .input-group-custom .form-select {
        padding-left: 45px;
    }

    .status-select-wrapper {
        position: relative;
    }

    .status-preview {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        gap: 6px;
        pointer-events: none;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .status-dot.status-scheduled { background: #f59e0b; }
    .status-dot.status-confirmed { background: #3b82f6; }
    .status-dot.status-completed { background: #22c55e; }
    .status-dot.status-canceled { background: #ef4444; }
    .status-dot.status-missed { background: #8b5cf6; }

    .time-duration-info {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fcd34d;
        border-radius: 12px;
        padding: 12px 16px;
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #d97706;
        font-weight: 600;
    }

    .time-duration-info i {
        font-size: 16px;
    }

    .staff-select-option {
        padding: 12px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 24px;
        border-top: 2px solid #e2e8f0;
    }

    .btn-save {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border: none;
        color: white;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        box-shadow: 0 6px 20px rgba(34, 197, 94, 0.35);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(34, 197, 94, 0.45);
        color: white;
    }

    .btn-cancel {
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        color: #64748b;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-cancel:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
        color: #475569;
    }

    .alert-info-custom {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 1px solid #93c5fd;
        border-radius: 16px;
        padding: 16px 20px;
        color: #1e40af;
        font-size: 14px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 24px;
    }

    .alert-info-custom i {
        font-size: 20px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .error-message {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .error-message i {
        font-size: 12px;
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }

    .char-counter {
        font-size: 12px;
        color: #94a3b8;
        text-align: right;
        margin-top: 4px;
    }

    .help-text {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    @media (max-width: 768px) {
        .page-header-card {
            padding: 24px;
        }

        .form-card {
            padding: 20px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn-save, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid edit-session-container">
    {{-- Page Header --}}
    <div class="page-header-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1>
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa buổi điều trị
                </h1>
                <p class="subtitle">Cập nhật thông tin chi tiết cho buổi điều trị</p>
            </div>
            <div class="session-badge-header">
                <i class="fas fa-calendar-day"></i>
                Buổi #{{ $session->session_no }}
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div>
                <strong>Có lỗi xảy ra:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Alert --}}
    <div class="alert-info-custom">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Lưu ý:</strong> Thay đổi thời gian hoặc nhân viên có thể ảnh hưởng đến lịch trình tổng thể. 
            Vui lòng kiểm tra kỹ trước khi lưu.
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.tsessions.update', $session) }}" method="POST" id="editSessionForm">
        @csrf 
        @method('PUT')

        {{-- Schedule Information --}}
        <div class="form-card">
            <h5 class="form-section-title">
                <i class="fas fa-clock"></i>
                Thông tin lịch trình
            </h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt"></i>
                        Thời gian bắt đầu
                        <span class="required">*</span>
                    </label>
                    <div class="input-group-custom">
                        <i class="input-icon fas fa-play-circle"></i>
                        <input type="datetime-local" 
                               name="scheduled_start"
                               id="scheduled_start"
                               value="{{ old('scheduled_start', $session->scheduled_start?->format('Y-m-d\TH:i')) }}" 
                               class="form-control @error('scheduled_start') is-invalid @enderror"
                               required>
                    </div>
                    @error('scheduled_start')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-calendar-check"></i>
                        Thời gian kết thúc
                        <span class="required">*</span>
                    </label>
                    <div class="input-group-custom">
                        <i class="input-icon fas fa-stop-circle"></i>
                        <input type="datetime-local" 
                               name="scheduled_end"
                               id="scheduled_end"
                               value="{{ old('scheduled_end', $session->scheduled_end?->format('Y-m-d\TH:i')) }}" 
                               class="form-control @error('scheduled_end') is-invalid @enderror"
                               required>
                    </div>
                    @error('scheduled_end')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div id="duration-info" class="time-duration-info" style="display: none;">
                <i class="fas fa-hourglass-half"></i>
                <span>Thời lượng: <strong id="duration-text">0 phút</strong></span>
            </div>
        </div>

        {{-- Assignment Information --}}
        <div class="form-card">
            <h5 class="form-section-title">
                <i class="fas fa-users"></i>
                Phân công
            </h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-user-md"></i>
                        Nhân viên thực hiện
                        <span class="required">*</span>
                    </label>
                    <div class="input-group-custom">
                        <i class="input-icon fas fa-user"></i>
                        <select name="staff_id" 
                                class="form-select @error('staff_id') is-invalid @enderror"
                                required>
                            <option value="">-- Chọn nhân viên --</option>
                            @if(isset($staffs))
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}" 
                                            {{ old('staff_id', $session->staff_id) == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                        @if($staff->specialty ?? false)
                                            - {{ $staff->specialty }}
                                        @endif
                                    </option>
                                @endforeach
                            @else
                                <option value="{{ $session->staff_id }}" selected>
                                    {{ $session->staff->name ?? 'Nhân viên #'.$session->staff_id }}
                                </option>
                            @endif
                        </select>
                    </div>
                    @error('staff_id')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Chọn nhân viên sẽ thực hiện buổi điều trị này
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-door-open"></i>
                        Phòng điều trị
                    </label>
                    <div class="input-group-custom">
                        <i class="input-icon fas fa-door-closed"></i>
                        <select name="room_id" 
                                class="form-select @error('room_id') is-invalid @enderror">
                            <option value="">-- Chọn phòng --</option>
                            @if(isset($rooms))
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" 
                                            {{ old('room_id', $session->room_id) == $room->id ? 'selected' : '' }}>
                                        Phòng {{ $room->name ?? $room->id }}
                                        @if($room->capacity ?? false)
                                            ({{ $room->capacity }} người)
                                        @endif
                                    </option>
                                @endforeach
                            @else
                                @if($session->room_id)
                                    <option value="{{ $session->room_id }}" selected>
                                        Phòng {{ $session->room_id }}
                                    </option>
                                @endif
                            @endif
                        </select>
                    </div>
                    @error('room_id')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Phòng sẽ được sử dụng cho buổi điều trị
                    </div>
                </div>
            </div>
        </div>

        {{-- Status & Notes --}}
        <div class="form-card">
            <h5 class="form-section-title">
                <i class="fas fa-info-circle"></i>
                Trạng thái & Ghi chú
            </h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-flag"></i>
                        Trạng thái
                        <span class="required">*</span>
                    </label>
                    <div class="status-select-wrapper">
                        <div class="input-group-custom">
                            <i class="input-icon fas fa-traffic-light"></i>
                            <select name="status" 
                                    id="status-select"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                @php
                                    $statuses = [
                                        'scheduled' => 'Đã lên lịch',
                                        'confirmed' => 'Đã xác nhận',
                                        'completed' => 'Hoàn thành',
                                        'canceled' => 'Đã hủy',
                                        'missed' => 'Bỏ lỡ'
                                    ];
                                @endphp
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" 
                                            {{ old('status', $session->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="status-preview">
                            <span class="status-dot status-{{ old('status', $session->status) }}"></span>
                        </div>
                    </div>
                    @error('status')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">
                        <i class="fas fa-sticky-note"></i>
                        Ghi chú
                    </label>
                    <textarea name="note" 
                              id="note-textarea"
                              class="form-control @error('note') is-invalid @enderror" 
                              rows="5"
                              maxlength="500"
                              placeholder="Nhập ghi chú về buổi điều trị này...">{{ old('note', $session->note) }}</textarea>
                    <div class="char-counter">
                        <span id="char-count">{{ strlen(old('note', $session->note ?? '')) }}</span>/500 ký tự
                    </div>
                    @error('note')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="form-card">
            <div class="form-actions">
                <a href="{{ url()->previous() }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    Hủy bỏ
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </form>
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

document.addEventListener('DOMContentLoaded', function() {
    // Calculate duration
    const startInput = document.getElementById('scheduled_start');
    const endInput = document.getElementById('scheduled_end');
    const durationInfo = document.getElementById('duration-info');
    const durationText = document.getElementById('duration-text');

    function calculateDuration() {
        if (startInput.value && endInput.value) {
            const start = new Date(startInput.value);
            const end = new Date(endInput.value);
            const diffMs = end - start;
            const diffMins = Math.round(diffMs / 60000);
            
            if (diffMins > 0) {
                durationInfo.style.display = 'flex';
                const hours = Math.floor(diffMins / 60);
                const mins = diffMins % 60;
                
                if (hours > 0) {
                    durationText.textContent = `${hours} giờ ${mins} phút`;
                } else {
                    durationText.textContent = `${mins} phút`;
                }
            } else {
                durationInfo.style.display = 'none';
            }
        }
    }

    startInput.addEventListener('change', calculateDuration);
    endInput.addEventListener('change', calculateDuration);
    calculateDuration(); // Initial calculation

    // Character counter for notes
    const noteTextarea = document.getElementById('note-textarea');
    const charCount = document.getElementById('char-count');

    if (noteTextarea && charCount) {
        noteTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    // Update status dot color
    const statusSelect = document.getElementById('status-select');
    const statusDot = document.querySelector('.status-preview .status-dot');

    if (statusSelect && statusDot) {
        statusSelect.addEventListener('change', function() {
            statusDot.className = 'status-dot status-' + this.value;
        });
    }

    // Form validation
    const form = document.getElementById('editSessionForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const start = new Date(startInput.value);
            const end = new Date(endInput.value);
            
            if (end <= start) {
                e.preventDefault();
                alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
                endInput.focus();
                return false;
            }
        });
    }
});
</script>
@endpush
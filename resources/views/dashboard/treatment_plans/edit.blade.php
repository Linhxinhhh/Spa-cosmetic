@extends('dashboard.layouts.app')

@section('content')
<style>
    .status-update-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Header */
    .status-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(30, 64, 175, 0.2);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .status-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(10deg);
        }
    }

    .status-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .status-header h1 i {
        font-size: 2rem;
    }

    .status-header p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        font-size: 1rem;
        position: relative;
        z-index: 2;
    }

    .plan-id-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Plan Info Card */
    .plan-info-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        animation: slideInDown 0.4s ease;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .info-title {
        color: #1e40af;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid #dbeafe;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-title i {
        color: #3b82f6;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        transform: translateY(-2px);
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        flex-shrink: 0;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .info-value {
        font-size: 1rem;
        color: #1e293b;
        font-weight: 600;
    }

    /* Service Type Badge */
    .service-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .service-type-badge.package {
        background: linear-gradient(135deg, #a78bfa, #8b5cf6);
        color: white;
    }

    .service-type-badge.single {
        background: linear-gradient(135deg, #60a5fa, #3b82f6);
        color: white;
    }

    .service-name {
        display: block;
        font-size: 0.9rem;
        color: #475569;
        margin-top: 4px;
    }

    /* Status Badges */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    .status-draft {
        background: #f3f4f6;
        color: #6b7280;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-scheduled {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-confirmed {
        background: #fef3c7;
        color: #b45309;
    }

    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .status-canceled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-expired {
        background: #f3f4f6;
        color: #374151;
    }

    /* Form Card */
    .form-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        animation: slideInDown 0.5s ease;
    }

    .form-title {
        color: #1e40af;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid #dbeafe;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-title i {
        color: #3b82f6;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
    }

    .form-label i {
        color: #3b82f6;
    }

    .form-select {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
        cursor: pointer;
        font-weight: 500;
    }

    .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .form-select:hover {
        border-color: #93c5fd;
    }

    /* Status Preview */
    .status-preview {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-top: 15px;
    }

    .status-preview-label {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 10px;
        font-weight: 500;
    }

    /* Button Group */
    .button-group {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #e9ecef;
    }

    .btn-save {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 14px 32px;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    .btn-cancel {
        background: white;
        color: #6b7280;
        padding: 14px 32px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
        color: #374151;
        text-decoration: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .status-update-container {
            padding: 15px;
        }

        .status-header {
            padding: 20px;
        }

        .status-header h1 {
            font-size: 1.5rem;
        }

        .plan-info-card,
        .form-card {
            padding: 20px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .button-group {
            flex-direction: column;
        }

        .button-group .btn-save,
        .button-group .btn-cancel {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="status-update-container">
    {{-- HEADER --}}
    <div class="status-header">
        <h1>
            <i class="fas fa-edit"></i>
            Cập nhật trạng thái kế hoạch liệu trình
            <span class="plan-id-badge">#{{ $plan->id }}</span>
        </h1>
        <p>Quản lý và cập nhật trạng thái kế hoạch điều trị</p>
    </div>

    {{-- THÔNG TIN KẾ HOẠCH --}}
    <div class="plan-info-card">
        <h2 class="info-title">
            <i class="fas fa-info-circle"></i>
            Thông tin kế hoạch
        </h2>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Khách hàng</div>
                    <div class="info-value">{{ $plan->customer->name ?? 'KH#' . $plan->customer_id }}</div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Dịch vụ / Gói</div>
                    <div class="info-value">
                        @if($plan->packageService)
                            <span class="service-type-badge package">
                                <i class="fas fa-cube"></i> Gói dịch vụ
                            </span>
                            <span class="service-name">{{ $plan->packageService->service_name }}</span>
                        @elseif($plan->singleService)
                            <span class="service-type-badge single">
                                <i class="fas fa-star"></i> Dịch vụ lẻ
                            </span>
                            <span class="service-name">{{ $plan->singleService->service_name }}</span>
                        @else
                            <span style="color: #94a3b8;">Không xác định</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Ngày bắt đầu</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($plan->start_date)->format('d/m/Y') }}</div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Số buổi</div>
                    <div class="info-value">{{ $plan->sessions()->count() }} buổi</div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Trạng thái hiện tại</div>
                    <div class="info-value">
                        @include('dashboard.treatment_plans._status-badge', ['status' => $plan->status])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM UPDATE --}}
    <div class="form-card">
        <form action="{{ route('admin.treatment-plans.update', $plan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <h2 class="form-title">
                <i class="fas fa-sync-alt"></i>
                Cập nhật trạng thái kế hoạch
            </h2>

            <div class="form-group">
                <label class="form-label" for="status">
                    <i class="fas fa-toggle-on"></i>
                    Chọn trạng thái mới
                </label>
                <select name="status" id="status" class="form-select" required>
                    <option value="draft" {{ $plan->status == 'draft' ? 'selected' : '' }}>
                        📝 Nháp
                    </option>
                    <option value="active" {{ $plan->status == 'active' ? 'selected' : '' }}>
                        ✅ Đang hoạt động
                    </option>
                    <option value="scheduled" {{ $plan->status == 'scheduled' ? 'selected' : '' }}>
                        📅 Đã lên lịch
                    </option>
                    <option value="confirmed" {{ $plan->status == 'confirmed' ? 'selected' : '' }}>
                        ✔️ Đã xác nhận
                    </option>
                    <option value="completed" {{ $plan->status == 'completed' ? 'selected' : '' }}>
                        🎉 Hoàn thành
                    </option>
                    <option value="canceled" {{ $plan->status == 'canceled' ? 'selected' : '' }}>
                        ❌ Đã huỷ
                    </option>
                    <option value="expired" {{ $plan->status == 'expired' ? 'selected' : '' }}>
                        ⏰ Hết hạn
                    </option>
                </select>

            </div>

            <div class="button-group">
                <a href="{{ route('admin.treatment-plans.show', $plan->id) }}" class="btn-cancel">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Status preview update
    document.getElementById('status').addEventListener('change', function() {
        const statusValue = this.value;
        const previewBadge = document.getElementById('statusPreview');
        
        // Remove all status classes
        previewBadge.className = 'status-badge';
        
        // Add new status class
        previewBadge.classList.add('status-' + statusValue);
        previewBadge.textContent = statusValue.toUpperCase();
    });
</script>

@endsection
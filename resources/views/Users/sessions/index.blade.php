@extends('Users.servicehome')

@push('styles')
<style>
    .sessions-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 32px 20px;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 32px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header h1 {
        color: white;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }

    .page-header .subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 15px;
        margin-bottom: 0;
        position: relative;
        z-index: 1;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        border-color: #667eea;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 12px;
    }

    .stat-icon.icon-total {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stat-icon.icon-upcoming {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .stat-icon.icon-completed {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
    }

    .stat-icon.icon-cancelled {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }

    .alert-modern {
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border: none;
        margin-bottom: 24px;
        font-weight: 500;
    }

    .alert-modern i {
        font-size: 20px;
    }

    .alert-modern.alert-success {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
    }

    .alert-modern.alert-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }

    .sessions-grid {
        display: grid;
        gap: 20px;
        margin-bottom: 32px;
    }

    .session-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .session-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #667eea, #764ba2);
    }

    .session-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        border-color: #e2e8f0;
    }

    .session-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        gap: 16px;
    }

    .session-number-badge {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 18px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        flex-shrink: 0;
    }

    .session-info {
        flex: 1;
    }

    .session-service {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .session-service i {
        color: #667eea;
        font-size: 20px;
    }

    .session-id {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 500;
    }

    .session-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-icon {
        width: 36px;
        height: 36px;
        background: #f8fafc;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        font-size: 16px;
        flex-shrink: 0;
    }

    .detail-content {
        flex: 1;
    }

    .detail-label {
        font-size: 11px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .detail-value {
        font-size: 14px;
        color: #475569;
        font-weight: 600;
    }

    .session-actions {
        display: flex;
        gap: 10px;
        padding-top: 20px;
        border-top: 2px solid #f1f5f9;
    }

    .btn-action {
        flex: 1;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        cursor: pointer;
    }

    .btn-reschedule {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-reschedule:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        color: white;
    }

    .btn-cancel {
        background: white;
        color: #ef4444;
        border: 2px solid #fee2e2;
    }

    .btn-cancel:hover {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-color: #fca5a5;
    }

    .btn-disabled {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
        border: 2px solid #e2e8f0;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .status-draft {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .status-pending {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
        border: 1px solid #fcd34d;
    }

    .status-scheduled {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #2563eb;
        border: 1px solid #93c5fd;
    }

    .status-confirmed {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        border: 1px solid #60a5fa;
    }

    .status-rescheduled {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #6366f1;
        border: 1px solid #a5b4fc;
    }

    .status-completed {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
        border: 1px solid #86efac;
    }

    .status-cancelled, .status-canceled {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
        border: 1px solid #fca5a5;
    }

    .status-no_show {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .empty-state {
        background: white;
        border-radius: 24px;
        padding: 60px 40px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    }

    .empty-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 24px;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #94a3b8;
    }

    .empty-title {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 12px;
    }

    .empty-text {
        font-size: 16px;
        color: #64748b;
        margin-bottom: 32px;
    }

    .btn-primary-large {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px 32px;
        border-radius: 14px;
        font-weight: 600;
        font-size: 16px;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.35);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: none;
        text-decoration: none;
    }

    .btn-primary-large:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(102, 126, 234, 0.45);
        color: white;
    }

    .pagination-wrapper {
        background: white;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    }

    @media (max-width: 768px) {
        .sessions-container {
            padding: 20px 16px;
        }

        .page-header {
            padding: 24px 20px;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }

        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .session-details {
            grid-template-columns: 1fr;
        }

        .session-actions {
            flex-direction: column;
        }

        .session-header {
            flex-direction: column;
        }
    }

    /* Cancel form inline */
    .cancel-form {
        display: inline;
        flex: 1;
    }
</style>
<style>
/* ===== ORANGE THEME OVERRIDES for My Sessions ===== */
:root{
  --c-primary-100:#FFEDD5;
  --c-primary-200:#FED7AA;
  --c-primary-300:#FDBA74;
  --c-primary-400:#FB923C;
  --c-primary-500:#F59E0B; /* cam chính */
  --c-primary-600:#F97316; /* cam hover */
  --c-primary-700:#EA580C;

  --g-header: linear-gradient(135deg, #F59E0B 0%, #F97316 60%, #EA580C 100%);
  --g-cta:    linear-gradient(135deg, #F59E0B 0%, #F97316 100%);
  --g-cta-hv: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
}

/* Header block */
.page-header{
  background: var(--g-header) !important;
  box-shadow: 0 10px 40px rgba(249, 115, 22, .25) !important;
}
.page-header::before{
  background: radial-gradient(circle, rgba(255,255,255,.14) 0%, transparent 70%) !important;
}

/* Stats */
.stat-card:hover{ border-color: var(--c-primary-500) !important; }
.stat-icon.icon-total{
  background: var(--g-header) !important; color:#fff !important;
}

/* Session cards & accents */
.session-card::before{
  background: linear-gradient(to bottom, var(--c-primary-500), var(--c-primary-600)) !important;
}
.session-number-badge{
  background: var(--g-header) !important;
  box-shadow: 0 4px 12px rgba(249,115,22,.3) !important;
}
.session-service i,
.detail-icon{ color: var(--c-primary-600) !important; }

/* Status badges – dùng cam cho “đã lên lịch/đã xác nhận/đã dời” */
.status-scheduled{
  background: linear-gradient(135deg, var(--c-primary-100), var(--c-primary-200)) !important;
  color: var(--c-primary-700) !important; border-color: var(--c-primary-300) !important;
}
.status-confirmed{
  background: linear-gradient(135deg, var(--c-primary-100), var(--c-primary-200)) !important;
  color: #9A4A08 !important; border-color: var(--c-primary-300) !important;
}
.status-rescheduled{
  background: linear-gradient(135deg, #FFEAD5, #FFD8A8) !important;
  color: var(--c-primary-700) !important; border-color: var(--c-primary-300) !important;
}

/* Buttons */
.btn-reschedule{
  background: var(--g-cta) !important; color:#fff !important;
  box-shadow: 0 4px 12px rgba(245,158,11,.28) !important;
}
.btn-reschedule:hover{ background: var(--g-cta-hv) !important; }

.btn-primary-large{
  background: var(--g-cta) !important; box-shadow: 0 6px 20px rgba(249,115,22,.35) !important;
}
.btn-primary-large:hover{ background: var(--g-cta-hv) !important; }

/* Pagination card tone nhè nhẹ */
.pagination-wrapper{ border:1px solid #F6E7D5 !important; }

/* Optional: alert success/danger viền cam nhẹ cho hợp tông */
.alert-modern.alert-success{ box-shadow: 0 4px 12px rgba(34,197,94,.12) !important; }
.alert-modern.alert-danger{  box-shadow: 0 4px 12px rgba(234,88,12,.15) !important; }
</style>

@endpush

@section('content')
<div class="sessions-container">
    {{-- Page Header --}}
    <div class="page-header">
        <h1>
            <i class="fas fa-calendar-check me-2"></i>Buổi điều trị của tôi
        </h1>
        <p class="subtitle">Quản lý và theo dõi lịch trình điều trị của bạn</p>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert-modern alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-modern alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @php
        // Map trạng thái
        $statusMap = [
            'draft'      => ['Nháp',            'draft'],
            'pending'    => ['Chờ xác nhận',    'pending'],
            'scheduled'  => ['Đã lên lịch',     'scheduled'],
            'confirmed'  => ['Đã xác nhận',     'confirmed'],
            'rescheduled'=> ['Đã dời lịch',     'rescheduled'],
            'completed'  => ['Hoàn thành',      'completed'],
            'cancelled'  => ['Đã hủy',          'cancelled'],
            'canceled'   => ['Đã hủy',          'canceled'],
            'no_show'    => ['Vắng mặt',        'no_show'],
        ];
        $labelOf = fn($st) => $statusMap[$st][0] ?? ucfirst($st);
        $classOf = fn($st) => $statusMap[$st][1] ?? 'draft';

        // Statistics
        $totalSessions = $sessions->total();
        $upcomingSessions = $sessions->filter(fn($s) => in_array($s->status, ['scheduled', 'confirmed']) && $s->scheduled_start?->greaterThan(now()))->count();
        $completedSessions = $sessions->where('status', 'completed')->count();
        $cancelledSessions = $sessions->whereIn('status', ['cancelled', 'canceled'])->count();
    @endphp

    @if($sessions->isNotEmpty())
        {{-- Statistics Cards --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon icon-total">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-number">{{ $totalSessions }}</div>
                <div class="stat-label">Tổng số buổi</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-upcoming">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number">{{ $upcomingSessions }}</div>
                <div class="stat-label">Sắp diễn ra</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number">{{ $completedSessions }}</div>
                <div class="stat-label">Đã hoàn thành</div>
            </div>
          
        </div>

        {{-- Sessions Grid --}}
       {{-- Sessions Grid --}}
<div class="sessions-grid">
    @foreach($sessions as $s)
        <div class="session-card">
            <div class="session-header">
                <div class="d-flex align-items-start gap-3 flex-1">
                    <div class="session-number-badge">
                        {{ $s->session_no }}
                    </div>
                    <div class="session-info">
                        <div class="session-service">
                            <i class="fas fa-spa"></i>
                            {{ $s->plan->packageService->service_name
                                ?? $s->plan->singleService->service_name
                                ?? 'Dịch vụ điều trị' }}
                        </div>
                        <div class="session-id">ID: #{{ $s->id }}</div>
                    </div>
                </div>

                <div>
                    <span class="status-badge status-{{ $classOf($s->status) }}">
                        <span class="status-dot"></span>
                        {{ $labelOf($s->status) }}
                    </span>
                </div>
            </div>

            <div class="session-details">
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Ngày điều trị</div>
                        <div class="detail-value">
                            {{ $s->scheduled_start?->format('d/m/Y') ?? 'Chưa xác định' }}
                        </div>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Thời gian</div>
                        <div class="detail-value">
                            @if($s->scheduled_start && $s->scheduled_end)
                                {{ $s->scheduled_start->format('H:i') }} - {{ $s->scheduled_end->format('H:i') }}
                            @else
                                Chưa xác định
                            @endif
                        </div>
                    </div>
                </div>

                @if($s->staff)
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Nhân viên</div>
                            <div class="detail-value">{{ $s->staff->name }}</div>
                        </div>
                    </div>
                @endif

                @if($s->room_id)
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Phòng</div>
                            <div class="detail-value">Phòng {{ $s->room_id }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="session-actions">
                @if(in_array($s->status, ['scheduled','confirmed']) && $s->scheduled_start?->greaterThan(now()))
                    {{-- Nút mở popup dời lịch --}}
                    <button type="button"
                            class="btn-action btn-reschedule"
                            data-bs-toggle="modal"
                            data-bs-target="#rescheduleSessionModal{{ $s->id }}">
                        <i class="fas fa-calendar-alt"></i>
                        Dời lịch
                    </button>

                    {{-- Form hủy buổi --}}
                    <form action="{{ route('users.customer.sessions.cancel', $s) }}"
                          method="POST"
                          class="cancel-form"
                          onsubmit="return confirm('Bạn chắc chắn muốn hủy buổi này?');">
                        @csrf
                        <input type="hidden" name="reason" value="Khách tự hủy qua website">
                        <button type="submit" class="btn-action btn-cancel w-100">
                            <i class="fas fa-times-circle"></i>
                            Hủy buổi
                        </button>
                    </form>
                @else
                    <button class="btn-action btn-disabled w-100" disabled>
                        <i class="fas fa-lock"></i>
                        Không thể thay đổi
                    </button>
                @endif
            </div>
        </div>

        {{-- Modal Dời lịch cho từng buổi --}}
        <div class="modal fade" id="rescheduleSessionModal{{ $s->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Dời lịch buổi #{{ $s->session_no }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('users.customer.sessions.update', $s) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <p>
                                Dịch vụ:
                                <strong>
                                    {{ $s->plan->packageService->service_name
                                        ?? $s->plan->singleService->service_name
                                        ?? '-' }}
                                </strong>
                                <br>
                                Thời gian hiện tại:
                                <strong>{{ $s->scheduled_start?->format('d/m/Y H:i') }}</strong>
                            </p>

                            <div class="mb-3">
                                <label class="form-label">Ngày mới</label>
                                <input type="date" name="date" class="form-control"
                                       value="{{ $s->scheduled_start?->format('Y-m-d') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Giờ mới</label>
                                <input type="time" name="time" class="form-control"
                                       value="{{ $s->scheduled_start?->format('H:i') }}">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endforeach
</div>

        {{-- Pagination --}}
        @if($sessions->hasPages())
            <div class="pagination-wrapper">
                {{ $sessions->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h2 class="empty-title">Chưa có buổi điều trị</h2>
            <p class="empty-text">
                Bạn chưa có buổi điều trị nào được lên lịch.<br>
                Hãy đặt lịch dịch vụ để bắt đầu hành trình làm đẹp của bạn.
            </p>
            <a href="{{ route('users.booking.create') ?? '#' }}" class="btn-primary-large">
                <i class="fas fa-plus-circle"></i>
                Đặt lịch ngay
            </a>
        </div>
    @endif
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

// Smooth scroll and animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    entry.target.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.session-card, .stat-card').forEach(card => {
        observer.observe(card);
    });
});
</script>
@endpush
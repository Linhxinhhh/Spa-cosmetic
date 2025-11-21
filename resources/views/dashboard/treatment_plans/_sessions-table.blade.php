<div class="sessions-table-wrapper">
    <div class="table-responsive">
        <table class="table table-sessions align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 80px">Buổi</th>
                    <th style="min-width: 200px">Thời gian</th>
                    <th style="min-width: 180px">Nội dung</th>
                    <th style="min-width: 160px">Nhân viên</th>
                    <th style="width: 120px">Phòng</th>
                    <th style="width: 140px">Trạng thái</th>
                    <th style="width: 100px" class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $s)
                    <tr class="session-row">
                        <td>
                            <div class="session-number">
                                <span class="session-badge">{{ $s->session_no }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="time-info">
                                <div class="date-time">
                                    <i class="fas fa-calendar-day me-2 text-primary"></i>
                                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($s->scheduled_start)->format('d/m/Y') }}</span>
                                </div>
                                <div class="time-range">
                                    <i class="fas fa-clock me-2 text-muted"></i>
                                    <span>{{ \Carbon\Carbon::parse($s->scheduled_start)->format('H:i') }}</span>
                                    <i class="fas fa-arrow-right mx-1 small text-muted"></i>
                                    <span>{{ \Carbon\Carbon::parse($s->scheduled_end)->format('H:i') }}</span>
                                    <span class="duration-badge ms-2">
                                        {{ \Carbon\Carbon::parse($s->scheduled_start)->diffInMinutes(\Carbon\Carbon::parse($s->scheduled_end)) }} phút
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="session-content">
                                <i class="fas fa-spa me-2 text-info"></i>
                                <span class="fw-medium">{{ $s->packageStep->title ?? 'Buổi '.$s->session_no }}</span>
                            </div>
                        </td>
                        <td>
                            @if($s->staff)
                                <div class="staff-info">
                                    <div class="staff-avatar">
                                        {{ strtoupper(substr($s->staff->name, 0, 1)) }}
                                    </div>
                                    <div class="staff-details">
                                        <div class="staff-name">{{ $s->staff->name }}</div>
                                        @if($s->staff->phone ?? false)
                                            <div class="staff-contact">
                                                <i class="fas fa-phone small"></i> {{ $s->staff->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">
                                    <i class="fas fa-user-slash me-1"></i>Chưa chỉ định
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($s->room_id)
                                <div class="room-badge">
                                    <i class="fas fa-door-open me-1"></i>
                                    Phòng {{ $s->room_id }}
                                </div>
                            @else
                                <span class="text-muted">
                                    <i class="fas fa-minus-circle me-1"></i>Chưa có
                                </span>
                            @endif
                        </td>
                        <td>
                            @include('dashboard.treatment_plans._status-badge', ['status' => $s->status])
                        </td>
                     <td>
                        <div class="action-buttons-small">
                            {{-- Nút xem chi tiết kế hoạch --}}
                            <a href="{{ route('admin.treatment-plans.show', $plan->id) }}" 
                            class="btn-action-sm btn-view-sm" 
                            title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Nút chỉnh sửa buổi --}}
                            <a href="{{ route('admin.tsessions.edit', $session->id ?? $s->id) }}" 
                            class="btn-action-sm btn-edit-sm" 
                            title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-sessions">
                                <div class="empty-icon">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <div class="empty-text">
                                    <h6 class="mb-1">Chưa có buổi điều trị</h6>
                                    <p class="text-muted mb-0">Hãy thêm buổi điều trị đầu tiên cho kế hoạch này</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.sessions-table-wrapper {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    margin-bottom: 24px;
}

.table-sessions {
    margin-bottom: 0;
}

.table-sessions thead {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.table-sessions thead th {
    padding: 16px 18px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    border: none;
    white-space: nowrap;
}

.table-sessions tbody td {
    padding: 18px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
}

.session-row {
    transition: all 0.2s ease;
}

.session-row:hover {
    background: #f8fafc;
    transform: scale(1.001);
}

.session-number {
    display: flex;
    justify-content: center;
}

.session-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.time-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.date-time {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #1e293b;
}

.time-range {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #64748b;
}

.duration-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1px solid #fcd34d;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    color: #d97706;
}

.session-content {
    display: flex;
    align-items: center;
    color: #475569;
}

.staff-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.staff-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 15px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.25);
}

.staff-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.staff-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
}

.staff-contact {
    font-size: 12px;
    color: #64748b;
}

.room-badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border: 1px solid #93c5fd;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: #2563eb;
}

.action-buttons-small {
    display: flex;
    gap: 6px;
    justify-content: flex-end;
}

.btn-action-sm {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: all 0.2s ease;
    font-size: 13px;
    cursor: pointer;
}

.btn-view-sm {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
}

.btn-view-sm:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-edit-sm {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #d97706;
}

.btn-edit-sm:hover {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.empty-sessions {
    padding: 48px 20px;
    text-align: center;
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 16px;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: #94a3b8;
}

.empty-text h6 {
    font-size: 16px;
    font-weight: 600;
    color: #475569;
}

.empty-text p {
    font-size: 14px;
}

/* Timeline indicator */
.session-row:not(:last-child) .session-number::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 100%;
    transform: translateX(-50%);
    width: 2px;
    height: 18px;
    background: linear-gradient(to bottom, #e2e8f0, transparent);
}

.session-number {
    position: relative;
}

/* Responsive */
@media (max-width: 768px) {
    .table-sessions thead th {
        font-size: 10px;
        padding: 12px 10px;
    }
    
    .table-sessions tbody td {
        padding: 12px 10px;
        font-size: 13px;
    }
    
    .time-info {
        font-size: 12px;
    }
    
    .staff-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    
    .duration-badge {
        font-size: 10px;
        padding: 2px 8px;
    }
}

/* Animation for new rows */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.session-row {
    animation: slideInUp 0.3s ease;
}

/* Status badge styles (if not already defined) */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.status-badge.status-pending {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #d97706;
    border: 1px solid #fcd34d;
}

.status-badge.status-completed {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #16a34a;
    border: 1px solid #86efac;
}

.status-badge.status-cancelled {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
    border: 1px solid #fca5a5;
}

.status-badge.status-in-progress {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
    border: 1px solid #93c5fd;
}
</style>

<script>
// Add Font Awesome if not already included
if (!document.querySelector('link[href*="font-awesome"]')) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
    document.head.appendChild(link);
}

// Add tooltips if Bootstrap is available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
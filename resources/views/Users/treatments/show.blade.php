@extends('Users.servicehome')

@section('title', 'Chi tiết liệu trình #'.$plan->id)

@section('content')
@php
    // Map trạng thái KẾ HOẠCH
    $planStatusMap = [
        'active'    => ['label' => 'Hoạt động',  'class' => 'success',   'icon' => 'bi-play-circle-fill'],
        'paused'    => ['label' => 'Tạm ngưng',  'class' => 'warning',   'icon' => 'bi-pause-circle-fill'],
        'cancelled' => ['label' => 'Đã hủy',     'class' => 'danger',    'icon' => 'bi-x-circle-fill'],
        'draft'     => ['label' => 'Nháp',       'class' => 'secondary', 'icon' => 'bi-file-earmark'],
        'completed' => ['label' => 'Hoàn thành', 'class' => 'primary',   'icon' => 'bi-check-circle-fill'],
    ];
    $planState = $planStatusMap[$plan->status] ?? ['label' => ucfirst($plan->status), 'class' => 'secondary', 'icon' => 'bi-question-circle'];

    // Map trạng thái BUỔI
    $sessionStatusMap = [
        'pending'   => ['label' => 'Chờ xác nhận',  'class' => 'warning',   'icon' => 'bi-clock'],
        'scheduled' => ['label' => 'Đã lên lịch',   'class' => 'info',      'icon' => 'bi-calendar-check'],
        'confirmed' => ['label' => 'Đã xác nhận',   'class' => 'primary',   'icon' => 'bi-check2-circle'],
        'completed' => ['label' => 'Đã hoàn thành', 'class' => 'success',   'icon' => 'bi-check-circle-fill'],
        'canceled'  => ['label' => 'Đã hủy',        'class' => 'danger',    'icon' => 'bi-x-circle'],
        'cancelled' => ['label' => 'Đã hủy',        'class' => 'danger',    'icon' => 'bi-x-circle'],
        'missed'    => ['label' => 'Bỏ lỡ',         'class' => 'secondary', 'icon' => 'bi-exclamation-triangle'],
        'no_show'   => ['label' => 'Không đến',     'class' => 'secondary', 'icon' => 'bi-person-x'],
        'draft'     => ['label' => 'Nháp',          'class' => 'light',     'icon' => 'bi-file-earmark'],
    ];

    $totalSessions = $plan->sessions->count();
    $completedSessions = $plan->sessions->where('status', 'completed')->count();
    $progressPercent = $totalSessions > 0 ? ($completedSessions / $totalSessions) * 100 : 0;
@endphp

<div class="container-fluid px-4 py-4">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('users.treatments.index') }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <!-- Treatment Header Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <h2 class="fw-bold mb-0">Liệu trình #{{ $plan->id }}</h2>
                        <span class="badge bg-{{ $planState['class'] }} px-3 py-2 fs-6">
                            <i class="bi {{ $planState['icon'] }} me-1"></i>
                            {{ $planState['label'] }}
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-heart-pulse text-primary fs-5 me-3 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block mb-1">Dịch vụ</small>
                                    <p class="mb-0 fw-semibold">
                                        @if($plan->packageService)
                                            {{ $plan->packageService->service_name }}
                                            <span class="badge bg-gradient ms-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <i class="bi bi-box-seam me-1"></i>Gói
                                            </span>
                                        @elseif($plan->singleService)
                                            {{ $plan->singleService->service_name }}
                                            <span class="badge bg-gradient ms-2" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                                <i class="bi bi-bookmark me-1"></i>Lẻ
                                            </span>
                                        @else
                                            <span class="text-muted">Không rõ dịch vụ</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-calendar3 text-success fs-5 me-3 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block mb-1">Ngày bắt đầu</small>
                                    <p class="mb-0 fw-semibold">{{ $plan->start_date?->format('d/m/Y') ?? 'Chưa xác định' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="text-center p-4 bg-light rounded-3">
                        <div class="mb-2">
                            <h3 class="fw-bold mb-0">{{ $completedSessions }}/{{ $totalSessions }}</h3>
                            <small class="text-muted">Buổi hoàn thành</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $progressPercent }}%;" 
                                 aria-valuenow="{{ $progressPercent }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">{{ round($progressPercent) }}% hoàn thành</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-calendar2-week text-primary me-2"></i>
                    Lịch các buổi điều trị
                </h5>
                @if(!$plan->sessions->isEmpty())
                    <span class="badge bg-light text-dark px-3 py-2">
                        Tổng: {{ $totalSessions }} buổi
                    </span>
                @endif
            </div>
        </div>

        <div class="card-body p-0">
            @if($plan->sessions->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mb-2">Chưa có buổi nào</h5>
                    <p class="text-muted small mb-0">Các buổi điều trị sẽ được lên lịch sớm</p>
                </div>
            @else
                <!-- Sessions Timeline -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3" style="width: 80px;">Buổi</th>
                                <th class="py-3">Nội dung</th>
                                <th class="py-3" style="width: 250px;">
                                    <i class="bi bi-clock me-1"></i>Thời gian
                                </th>
                                <th class="py-3 text-center" style="width: 180px;">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plan->sessions as $s)
                                @php
                                    $st = $sessionStatusMap[$s->status] ?? ['label' => ucfirst($s->status), 'class' => 'secondary', 'icon' => 'bi-question-circle'];
                                    $isPast = $s->scheduled_start && $s->scheduled_start->isPast();
                                @endphp
                                <tr class="session-row {{ $s->status === 'completed' ? 'table-success-subtle' : '' }}">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="badge rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                                  style="width: 40px; height: 40px; font-size: 1rem;">
                                                {{ $s->session_no }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold text-dark mb-1">
                                            {{ $s->packageStep->title ?? 'Buổi '.$s->session_no }}
                                        </div>
                                        @if($s->packageStep && $s->packageStep->description)
                                            <small class="text-muted">{{ Str::limit($s->packageStep->description, 60) }}</small>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($s->scheduled_start)
                                            <div class="d-flex flex-column gap-1">
                                                <span class="text-dark">
                                                    <i class="bi bi-calendar-event me-1 text-primary"></i>
                                                    {{ $s->scheduled_start->format('d/m/Y') }}
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $s->scheduled_start->format('H:i') }} - {{ $s->scheduled_end?->format('H:i') ?? 'N/A' }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted small">Chưa lên lịch</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-{{ $st['class'] }} px-3 py-2">
                                            <i class="bi {{ $st['icon'] }} me-1"></i>
                                            {{ $st['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.session-row {
    transition: all 0.2s ease;
}

.session-row:hover {
    background-color: rgba(0, 123, 255, 0.03);
    transform: translateX(2px);
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

.badge {
    font-weight: 500;
}

.card {
    border-radius: 12px;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.table-success-subtle {
    background-color: rgba(25, 135, 84, 0.05);
}

.badge.rounded-circle {
    font-weight: 600;
}
</style>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush
@endsection
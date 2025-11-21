@extends('Users.servicehome')

@section('title', 'Liệu trình của tôi')

@section('content')
@php
    $planStatusMap = [
        'active'    => ['label' => 'Hoạt động',   'class' => 'success',   'icon' => 'bi-play-circle-fill'],
        'paused'    => ['label' => 'Tạm ngưng',   'class' => 'warning',   'icon' => 'bi-pause-circle-fill'],
        'cancelled' => ['label' => 'Đã hủy',      'class' => 'danger',    'icon' => 'bi-x-circle-fill'],
        'canceled'  => ['label' => 'Đã hủy',      'class' => 'danger',    'icon' => 'bi-x-circle-fill'],
        'completed' => ['label' => 'Hoàn thành',  'class' => 'primary',   'icon' => 'bi-check-circle-fill'],
        'draft'     => ['label' => 'Nháp',        'class' => 'secondary', 'icon' => 'bi-file-earmark'],
         'expired'   => ['label' => 'Hết hạn',     'class' => 'dark',      'icon' => 'bi-clock-history'],

    ];
@endphp

<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold mb-1">Liệu trình của tôi</h2>
                    <p class="text-muted mb-0">Quản lý và theo dõi các liệu trình điều trị của bạn</p>
                </div>
                @if(!$plans->isEmpty())
                    <div class="badge bg-light text-dark fs-6 px-3 py-2">
                        <i class="bi bi-calendar-check me-1"></i>
                        Tổng: <strong>{{ $plans->total() }}</strong> liệu trình
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($plans->isEmpty())
        <!-- Empty State -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <div class="mb-4">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Chưa có liệu trình nào</h4>
                        <p class="text-muted mb-4">
                            Bạn chưa có liệu trình điều trị nào. Hãy đặt lịch với chúng tôi để bắt đầu hành trình chăm sóc sức khỏe.
                        </p>
                        <a href="{{ route('services.index') }}" class="btn btn-primary px-4">
                            <i class="bi bi-plus-circle me-2"></i>Đặt lịch ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Treatment Plans Grid -->
        <div class="row g-4">
            @foreach($plans as $plan)
                @php
                    $st = $planStatusMap[$plan->status] ?? ['label' => ucfirst($plan->status), 'class' => 'secondary', 'icon' => 'bi-question-circle'];
                    $serviceName = $plan->packageService?->service_name ?? $plan->singleService?->service_name ?? 'Không rõ dịch vụ';
                    $serviceType = $plan->packageService ? 'package' : 'single';
                    $sessionsCount = $plan->sessions_count ?? $plan->sessions()->count();
                @endphp
                
                <div class="col-lg-6 col-xl-4">
                    <a href="{{ route('users.treatments.show', $plan->id) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 treatment-card">
                            <div class="card-body p-4">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark px-3 py-2">
                                            #{{ $plan->id }}
                                        </span>
                                        <span class="badge bg-{{ $st['class'] }} px-3 py-2">
                                            <i class="bi {{ $st['icon'] }} me-1"></i>
                                            {{ $st['label'] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Service Info -->
                                <div class="mb-3">
                                    <h5 class="fw-bold mb-2 text-dark">{{ $serviceName }}</h5>
                                    @if($plan->packageService)
                                        <span class="badge bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="bi bi-box-seam me-1"></i>Gói dịch vụ
                                        </span>
                                    @elseif($plan->singleService)
                                        <span class="badge bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                            <i class="bi bi-bookmark me-1"></i>Dịch vụ lẻ
                                        </span>
                                    @endif
                                </div>

                                <!-- Details -->
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-calendar3 me-2"></i>
                                        <small>Bắt đầu: <strong class="text-dark">{{ \Carbon\Carbon::parse($plan->start_date)->format('d/m/Y') }}</strong></small>
                                    </div>
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-clock-history me-2"></i>
                                        <small>Số buổi: <strong class="text-dark">{{ $sessionsCount }} buổi</strong></small>
                                    </div>
                                </div>

                                <!-- Progress Bar (optional - if you have completion data) -->
                                @if($plan->status === 'active' && $sessionsCount > 0)
                                    @php
                                        $completedSessions = $plan->sessions()->where('status', 'completed')->count();
                                        $progress = ($completedSessions / $sessionsCount) * 100;
                                    @endphp
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Tiến độ</small>
                                            <small class="fw-bold">{{ round($progress) }}%</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $progress }}%;" 
                                                 aria-valuenow="{{ $progress }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-light border-0 p-3">
                                <div class="d-flex justify-content-end align-items-center">
                                    <small class="text-primary fw-semibold">
                                        Xem chi tiết <i class="bi bi-arrow-right ms-1"></i>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $plans->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.treatment-card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.treatment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
}

.badge {
    font-weight: 500;
    border-radius: 6px;
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
}

.card-footer {
    border-radius: 0 0 12px 12px !important;
}
</style>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush
@endsection
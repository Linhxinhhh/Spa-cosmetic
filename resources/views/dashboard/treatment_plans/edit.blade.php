@extends('dashboard.layouts.app')

@section('content')
<div class="container py-4">

    <h2 class="mb-4 fw-bold">Cập nhật trạng thái kế hoạch liệu trình #{{ $plan->id }}</h2>

    {{-- THÔNG TIN KẾ HOẠCH --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Thông tin kế hoạch</h5>

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Khách hàng:</strong><br>
                    {{ $plan->customer->name ?? 'KH#' . $plan->customer_id }}
                </div>

                <div class="col-md-4">
                    <strong>Dịch vụ / Gói:</strong><br>
                    @if($plan->packageService)
                        <span class="badge bg-primary">Gói dịch vụ</span>
                        <div class="small mt-1">{{ $plan->packageService->service_name }}</div>
                    @elseif($plan->singleService)
                        <span class="badge bg-secondary">Dịch vụ lẻ</span>
                        <div class="small mt-1">{{ $plan->singleService->service_name }}</div>
                    @else
                        <span class="text-muted small">Không xác định</span>
                    @endif
                </div>

                <div class="col-md-4">
                    <strong>Ngày bắt đầu:</strong><br>
                    {{ \Carbon\Carbon::parse($plan->start_date)->format('d/m/Y') }}
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <strong>Số buổi:</strong><br>
                    <span class="badge bg-light text-dark">
                        {{ $plan->sessions()->count() }} buổi
                    </span>
                </div>

                <div class="col-md-4">
                    <strong>Trạng thái hiện tại:</strong><br>
                    @include('dashboard.treatment_plans._status-badge', ['status' => $plan->status])
                </div>
            </div>
        </div>
    </div>

    {{-- FORM UPDATE --}}
    <form action="{{ route('admin.treatment-plans.update', $plan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <label class="form-label fw-semibold">Cập nhật trạng thái kế hoạch</label>
                <select name="status" class="form-control" required>
                    <option value="draft"     {{ $plan->status == 'draft'     ? 'selected' : '' }}>Nháp</option>
                    <option value="active"    {{ $plan->status == 'active'    ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="scheduled" {{ $plan->status == 'scheduled' ? 'selected' : '' }}>Đã lên lịch</option>
                    <option value="confirmed" {{ $plan->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="completed" {{ $plan->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="canceled"  {{ $plan->status == 'canceled'  ? 'selected' : '' }}>Đã huỷ</option>
                    <option value="expired"   {{ $plan->status == 'expired'   ? 'selected' : '' }}>Hết hạn</option>
                </select>

            </div>
        </div>

        <button class="btn btn-primary px-4 fw-semibold">Lưu thay đổi</button>
        <a href="{{ route('admin.treatment-plans.show', $plan->id) }}" class="btn btn-secondary px-4">Quay lại</a>
    </form>

</div>
@endsection

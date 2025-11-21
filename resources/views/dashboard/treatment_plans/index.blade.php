@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Kế hoạch liệu trình')
@section('page-title', 'Kế hoạch liệu trình')

@push('styles')
<link href="{{asset('admin/giaodien/css/style.css')}}" rel="stylesheet">
@endpush
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp
@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="service-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size:2.5rem;font-weight:700;">
                    <i class="fas fa-calendar-alt mr-3"></i>Quản lý kế hoạch liệu trình
                </h1>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-md-end gap-2">
                    {{-- Xuất Excel --}}
                    <a href="" class="btn-excel">
                        <i class="fas fa-download"></i> Xuất Excel
                    </a>
                    {{-- Thêm mới --}}
                    <a href="{{ route('admin.treatment-plans.create') }}" class="btn-add">
                        <i class="fas fa-plus me-1"></i> Thêm mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-modern mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        {{-- Search --}}
        <form action="{{ route('admin.treatment-plans.index') }}" method="GET"
              class="d-flex gap-2 align-items-center bg-white rounded-xl shadow-lg px-3 py-2 border border-blue-100">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Tìm kiếm theo khách hàng, dịch vụ..."
                   class="px-3 py-2 border border-gray-200 rounded-lg"
                   style="min-width:260px; width:300px">

            {{-- giữ lại params lọc khi search --}}
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="type" value="{{ request('type') }}">
            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg"><i class="fas fa-search mr-1"></i></button>
        </form>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.treatment-plans.index') }}" 
              class="d-flex gap-2 align-items-center bg-white rounded-xl shadow-lg px-3 py-2 border border-blue-100">

       <select name="status" class="px-7 py-2 border border-gray-200 rounded-lg">
            <option value="">Tất cả trạng thái</option>
            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>
                Đang hoạt động
            </option>
            <option value="scheduled" {{ request('status')=='scheduled' ? 'selected' : '' }}>
                Đã lên lịch
            </option>
            <option value="confirmed" {{ request('status')=='confirmed' ? 'selected' : '' }}>
                Đã xác nhận
            </option>
            <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>
                Hoàn thành
            </option>

            <option value="canceled" {{ request('status')=='canceled' ? 'selected' : '' }}>
                Đã hủy
            </option>
            <option value="expired" {{ request('status')=='expired' ? 'selected' : '' }}>
                Hết hạn
            </option>
        </select>


            <select name="type" class="px-7 py-2 border border-gray-200 rounded-lg">
                <option value="">Loại dịch vụ</option>
                <option value="single" {{ request('type')==='single' ? 'selected' : '' }}>Dịch vụ lẻ</option>
                <option value="package" {{ request('type')==='package' ? 'selected' : '' }}>Gói dịch vụ</option>
            </select>

            <button type="submit" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg">
                <i class="fas fa-filter mr-1"></i>Lọc
            </button>
            <a href="{{ route('admin.treatment-plans.index') }}" class="px-3 py-2 bg-gray-50 text-gray-700 rounded-lg">Xoá lọc</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th style="width:80px">#ID</th>
                    <th class="w-25" style="min-width:260px">Khách hàng</th>
                    <th>Dịch vụ/Gói</th>
                    <th>Ngày bắt đầu</th>
                    <th>Trạng thái</th>
                    <th>Số buổi</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                    <tr>
                        <td><strong>#{{ $plan->id }}</strong></td>
                        <td class="text-left w-25">
                            <strong>{{ $plan->customer->name ?? 'KH#' . $plan->customer_id }}</strong>
                        </td>
                        <td>
                            @if($plan->packageService)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Gói Dịch vụ</span>
                                <div class="text-sm mt-1">{{ $plan->packageService->service_name }}</div>
                            @elseif($plan->singleService)
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Dịch vụ Lẻ</span>
                                <div class="text-sm mt-1">{{ $plan->singleService->service_name }}</div>
                            @else
                                <span class="text-muted text-sm">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm text-muted">{{ Carbon::parse($plan->start_date)->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            @include('dashboard.treatment_plans._status-badge', ['status' => $plan->status])
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border border-secondary-subtle">{{ $plan->sessions_count ?? $plan->sessions()->count() }} buổi</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.treatment-plans.show', $plan->id) }}" class="btn btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.treatment-plans.edit', $plan->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                   
                                </a>
                           
                            
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>Chưa có kế hoạch liệu trình nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
          {{-- Pagination --}}
          @if($plans instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="bg-white border-t border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">

        <!-- TEXT HIỂN THỊ -->
        <div class="text-sm text-gray-700">
            Hiển thị 
            <span class="font-medium">{{ $plans->firstItem() }}</span> 
            đến 
            <span class="font-medium">{{ $plans->lastItem() }}</span> 
            trong tổng số 
            <span class="font-medium">{{ $plans->total() }}</span> 
            kết quả
        </div>

        <!-- PAGINATION BUTTONS -->
        <div class="flex items-center space-x-2">

            {{-- Nút Trước --}}
            <a href="{{ $plans->previousPageUrl() ?? '#' }}"
               class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 
               {{ $plans->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                <i class="fas fa-chevron-left mr-1"></i> Trước
            </a>

            {{-- Trang hiện tại --}}
            <span class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                {{ $plans->currentPage() }}
            </span>

            {{-- Nút Sau --}}
            <a href="{{ $plans->nextPageUrl() ?? '#' }}"
               class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 
               {{ !$plans->hasMorePages() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                Sau <i class="fas fa-chevron-right ml-1"></i>
            </a>

        </div>

    </div>
</div>
@endif
 
    </div>
  
</div>
<style>
    .btn-add {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border: none; color: #fff;
        padding: 12px 20px; border-radius: 12px;
        font-weight: 600; box-shadow: 0 6px 18px rgba(37,99,235,.35);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(37,99,235,.45); }

    /* Nút Export Excel (gradient xanh như mock) */
    .btn-excel {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        border: none; color: #fff;
        padding: 12px 20px; border-radius: 12px;
        font-weight: 600; box-shadow: 0 6px 18px rgba(59,130,246,.35);
        display: inline-flex; align-items: center; gap:.5rem;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .btn-excel:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(59,130,246,.45); }
</style>
@push('scripts')
<script>
    // Add Font Awesome if not already included
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
        document.head.appendChild(link);
    }
</script>
<script src="{{asset('admin/giaodien/js/main.js')}}"></script>
@endpush

@endsection
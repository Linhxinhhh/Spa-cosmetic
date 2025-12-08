@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị dịch vụ')
@section('page-title', 'Dịch vụ')

@push('styles')
<link href="{{ asset('admin/giaodien/css/style.css') }}" rel="stylesheet">

@endpush

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="service-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="mb-2" style="font-size:2.6rem;font-weight:800">
                    <i class="fas fa-concierge-bell me-3"></i> Quản lý dịch vụ
                </h1>
                <p class="mb-0 opacity-90">Tổng cộng: <strong>{{ $services->total() }}</strong> dịch vụ</p>
            </div>
            <div class="col-lg-4 text-left">
                <div class="d-flex justify-content-start gap-3">
                    <a href="#" class="btn-excel"><i class="fas fa-file-excel"></i> Xuất Excel</a>
                    <a href="{{ route('admin.services.create') }}" class="btn-add"><i class="fas fa-plus me-2"></i>Thêm dịch vụ</a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filter -->
 <div class="row mb-4 g-3 align-items-center">
    <!-- Form tìm kiếm -->
    <div class="col-xl-5">
        <form action="{{ route('admin.services.index') }}" method="GET" class="input-group shadow-sm w-100">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control py-3"
                   placeholder="Tìm tên, mô tả dịch vụ..."
                   style="border-radius:12px 0 0 12px;">
            <button class="btn btn-primary px-4" style="border-radius:0 12px 12px 0;">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Form lọc -->
    <div class="col-xl-7">
        <form method="GET" class="d-flex flex-nowrap gap-2 align-items-center justify-content-end">
            <select name="status" class="form-select w-auto py-2">
                <option value="">Trạng thái</option>
                <option value="1" {{ request('status')=='1'?'selected':'' }}>Hoạt động</option>
                <option value="0" {{ request('status')=='0'?'selected':'' }}>Tạm ngưng</option>
            </select>

            <select name="category_id" class="form-select w-auto py-2">
                <option value="">Danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}" {{ request('category_id')==$cat->category_id?'selected':'' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>

            <select name="type" class="form-select w-auto py-2">
                <option value="">Loại</option>
                <option value="Lẻ" {{ request('type')=='Lẻ'?'selected':'' }}>Lẻ</option>
                <option value="Gói" {{ request('type')=='Gói'?'selected':'' }}>Gói</option>
            </select>

            <div class="form-check ms-3">
                <input class="form-check-input" type="checkbox" name="featured" value="1" {{ request('featured')?'checked':'' }}>
                <label class="form-check-label text-primary fw-600">Nổi bật</label>
            </div>

            <button type="submit" class="btn btn-outline-primary px-4">Lọc</button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary px-4">Xóa lọc</a>
        </form>
    </div>
</div>


    <!-- Table -->
    <div class="card border-0 shadow rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Tên dịch vụ</th>
                        <th>Danh mục</th>
                        <th>Loại</th>
                        <th>Giá</th>
                        <th>Thời lượng</th>
                        <th>Nổi bật</th>
                        <th class="text-center">Hình ảnh</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr class="align-middle">
                            <td class="text-center"><strong>#{{ $service->service_id }}</strong></td>
                            <td>
                                <div class="fw-bold">{{ $service->service_name }}</div>
                                @if($service->short_desc)
                                    <small class="text-muted">{{ Str::limit($service->short_desc, 60) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info text-dark px-3 py-2 rounded-pill">
                                    {{ optional($service->category)->category_name ?? '—' }}
                                </span>
                            </td>
                            <td><span class="chip chip-type">{{ $service->type == 'Lẻ' ? 'LẺ' : 'GÓI' }}</span></td>
                            <td>
                                @php
                                    $sale = $service->price_sale;
                                    $orig = $service->price_original;
                                @endphp
                                @if($sale && $sale > 0 && $sale < $orig)
                                    <div>
                                        <span class="text-danger fw-bold fs-5">{{ number_format($sale) }}đ</span>
                                        <del class="text-muted small">{{ number_format($orig) }}đ</del>
                                    </div>
                                @else
                                    <span class="fw-bold text-success fs-5">{{ number_format($orig) }}đ</span>
                                @endif
                            </td>
                            <td class="text-center"><strong class="text-primary">{{ $service->duration }}</strong> phút</td>
                            <td class="text-center">
                                @if($service->is_featured)
                                    <span class="chip chip-featured">Nổi bật</span>
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </td>

                            <!-- CỘT ẢNH – SIÊU ỔN ĐỊNH, KHÔNG LỖI -->
                            <!-- CỘT ẢNH – HOÀN HẢO 100%, KHÔNG LỖI DÙ CÓ HAY KHÔNG CÓ ẢNH -->
<td class="text-center">
@php
    $images = $service->images ?? collect();

    $firstUrl  = optional($images->first())->image_url;
    $secondUrl = optional($images->skip(1)->first())->image_url;

    $first  = $firstUrl ? src_img_get($firstUrl) : src_img_get($service->thumbnail);
    $second = $secondUrl ? src_img_get($secondUrl) : null;
    $total  = $images->count();
@endphp

<div class="thumb-wrap position-relative d-inline-block">
    {{-- Ảnh chính --}}
    <img class="img-primary rounded-3 shadow"
         src="{{ $first }}"
         style="width:90px;height:90px;object-fit:cover"
         onerror="this.src='{{ asset("images/default-service.jpg") }}'"
         loading="lazy">

    {{-- Ảnh chi tiết (không phải main) --}}
    @if($second)
        <img class="img-secondary rounded-3"
             src="{{ $second }}"
             style="width:90px;height:90px;object-fit:cover"
             loading="lazy">
    @endif

    {{-- Badge số lượng ảnh còn lại --}}
    @if($total > 1)
        <div class="badge-count">+{{ $total - 1 }}</div>
    @endif
</div>

</td>


                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.services.edit', $service->service_id) }}"
                                       class="btn btn-warning btn-sm rounded-pill px-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service->service_id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Xóa dịch vụ «{{ $service->service_name }}»?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-delete btn-sm rounded-pill px-3">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-4x mb-3"></i>
                                <p class="fs-5">Chưa có dịch vụ nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer bg-white border-top">
            <div class="row align-items-center">
                <div class="col-md-6 text-muted">
                    Hiển thị {{ $services->firstItem() }} đến {{ $services->lastItem() }} trong {{ $services->total() }} dịch vụ
                </div>
                <div class="col-md-6 text-end">
                    {{ $services->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
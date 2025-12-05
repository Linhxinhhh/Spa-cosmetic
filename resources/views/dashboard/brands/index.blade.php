@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị thương hiệu')
@section('page-title', 'Danh mục thương hiệu')

@push('styles')
<link href="{{ asset('admin/giaodien/css/style.css') }}" rel="stylesheet">
@endpush

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="service-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size:2.5rem;font-weight:700;">
                    <i class="fas fa-tags mr-3"></i>Quản lý thương hiệu
                </h1>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-md-end gap-2">
                
                    {{-- Thêm mới --}}
                    <a href="{{ route('admin.brands.create') }}" class="btn-add">
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
        <form action="{{ route('admin.brands.index') }}" method="GET"
              class="d-flex gap-2 align-items-center bg-white rounded-xl shadow-lg px-3 py-2 border border-blue-100">
            <input type="text" name="keyword" value="{{ request('keyword') }}"
                   placeholder="Tìm kiếm thương hiệu theo tên, mô tả..."
                   class="px-3 py-2 border border-gray-200 rounded-lg"
                   style="min-width:260px; width:300px">
            {{-- Giữ params lọc --}}
            <input type="hidden" name="status" value="{{ request('status') }}">
            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg"><i class="fas fa-search mr-1"></i></button>
        </form>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.brands.index') }}" 
              class="d-flex gap-2 align-items-center bg-white rounded-xl shadow-lg px-3 py-2 border border-blue-100">
            <select name="status" class="px-7 py-2 border border-gray-200 rounded-lg">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Đang bán</option>
                <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Ngưng bán</option>
                <option value="2" {{ request('status')==='2' ? 'selected' : '' }}>Hết hàng</option>
            </select>

            <button type="submit" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg">
                <i class="fas fa-filter mr-1"></i>Lọc
            </button>
            <a href="{{ route('admin.brands.index') }}" class="px-3 py-2 bg-gray-50 text-gray-700 rounded-lg">Xoá lọc</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th style="width:80px">#ID</th>
                    <th style="min-width:200px">Tên thương hiệu</th>
                    <th style="min-width:200px" >Logo</th>
                    <th style="min-width:200px">Mô tả</th>
                    <th style="min-width:200px">Trạng thái</th>
                    <th style="min-width:200px">Hành động</th> 
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                    <tr>
                        <td><strong>#{{ $brand->brand_id }}</strong></td>
                        <td class="text-center">
                            <strong>{{ $brand->brand_name }}</strong><br>
                            @if($brand->description)
                                <small class="text-muted">{{ Str::limit($brand->description, 80) }}</small>
                            @endif
                        </td>
                    <td style="display: flex; align-items: center; justify-content: center; text-align: center; vertical-align: middle;">
    @if($brand->logo)
        @php
            $logoPath = $brand->logo;
            $logoSrc = src_img_get( $logoPath);
            
        @endphp
        @if($logoSrc)
            <img style="max-width: 150px; height: auto; display: block; margin: 0 auto; object-fit: contain;" src="{{ $logoSrc }}" class="service-image" alt="{{ $brand->brand_name }}">
        @else
            <div style="display: flex; align-items: center; justify-content: center; width: 150px; height: 100px; background: #f8f9fa; border-radius: 8px;">
                <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
            </div>
        @endif
    @else
        <div style="display: flex; align-items: center; justify-content: center; width: 150px; height: 100px; background: #f8f9fa; border-radius: 8px;">
            <span class="text-muted">Không có logo</span>
        </div>
    @endif
</td>
                        <td>{{ $brand->description ?? 'Chưa có mô tả' }}</td>
                        <td>
                            <span class="badge {{ $brand->status == 1 ? 'bg-success' : ($brand->status == 0 ? 'bg-danger' : 'bg-warning') }}">
                                {{ $brand->status == 1 ? 'Đang bán' : ($brand->status == 0 ? 'Ngưng bán' : 'Hết hàng') }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.brands.edit', $brand->brand_id) }}" class="btn btn-edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.brands.destroy', $brand->brand_id) }}" method="POST" onsubmit="return confirm('Xoá thương hiệu này?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete" title="Xoá"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-tags fa-2x mb-2"></i><br>Chưa có thương hiệu nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination Footer --}}
    @if($brands->hasPages())
    <div class="bg-white border-top px-3 py-3 d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Hiển thị
            <span class="font-weight-bold">{{ $brands->firstItem() }}</span> –
            <span class="font-weight-bold">{{ $brands->lastItem() }}</span>
            trong tổng số
            <span class="font-weight-bold">{{ $brands->total() }}</span> kết quả
        </div>
        <div>
            {{ $brands->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>
    @endif
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
    
    .service-image { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; }
    .chip { padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 500; }
    .chip-type { background: #e0f2fe; color: #0369a1; }
    .chip-featured { background: #fef3c7; color: #d97706; }
    .price-display { font-weight: bold; color: #dc2626; font-size: 1.1em; }
    .price-old { text-decoration: line-through; color: #6b7280; font-size: 0.9em; }
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
<script src="{{ asset('admin/giaodien/js/main.js') }}"></script>
@endpush
@endsection
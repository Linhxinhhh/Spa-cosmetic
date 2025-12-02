@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị nhân viên')
@section('page-title', 'Nhân viên')

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
                    <i class="fas fa-users mr-3"></i>Quản lý nhân viên
                </h1>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-md-end gap-2">
                  
                    {{-- Thêm mới --}}
                    <a href="{{ route('admin.staffs.create') }}" class="btn-add">
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
        <form action="{{ route('admin.staffs.index') }}" method="GET"
              class="d-flex gap-2 align-items-center bg-white rounded-xl shadow-lg px-3 py-2 border border-blue-100">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Tìm kiếm nhân viên theo tên, email..."
                   class="px-3 py-2 border border-gray-200 rounded-lg"
                   style="min-width:260px; width:300px">
            {{-- Giữ params lọc --}}
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="position" value="{{ request('position') }}">
            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg"><i class="fas fa-search mr-1"></i></button>
        </form>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.staffs.index') }}" 
              class="d-flex gap-2 align-items-center bg-white rounded-xl shadow-lg px-3 py-2 border border-blue-100">
            <select name="status" class="px-7 py-2 border border-gray-200 rounded-lg">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Không hoạt động</option>
            </select>

            <select name="position" class="px-3 py-2 border border-gray-200 rounded-lg">
                <option value="">Tất cả vị trí</option>
                {{-- Giả sử pass $positions từ controller --}}
                @foreach($positions ?? [] as $pos)
                    <option value="{{ $pos->id }}" {{ request('position') == $pos->id ? 'selected' : '' }}>
                        {{ $pos->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg">
                <i class="fas fa-filter mr-1"></i>Lọc
            </button>
            <a href="{{ route('admin.staffs.index') }}" class="px-3 py-2 bg-gray-50 text-gray-700 rounded-lg">Xoá lọc</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th style="width:80px">#ID</th>
                    <th style="min-width:60px">Avatar</th>
                    <th style="min-width:200px">Tên nhân viên</th>
                    <th style="min-width:180px">Email</th>
                    <th>SĐT</th>
                    <th>Vị trí</th>
                  
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffs as $staff)
                    <tr>
                        <td><strong>#{{ $staff->staff_id }}</strong></td>
                        <td>
                            <img src="{{ $staff->avatar_url }}" alt="Avatar" width="50" height="50" class="rounded-circle" 
                                 onerror="this.src='{{ asset('images/no-avatar.png') }}'">
                        </td>
                        <td class="text-left">
                            <strong>{{ $staff->full_name }}</strong><br>
                            @if($staff->user)
                                <small class="text-muted">User: {{ $staff->user->name ?? $staff->user->email }}</small>
                            @endif
                        </td>
                        <td>{{ $staff->email }}</td>
                        <td>{{ $staff->phone }}</td>
                        <td>{{ $staff->position }}</td>
                      
                        <td>
                            <span class="badge {{ $staff->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $staff->status ? 'Hoạt động' : 'Không hoạt động' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.staffs.show', $staff->staff_id) }}" class="btn btn-info btn-sm" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.staffs.edit', $staff->staff_id) }}" class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.staffs.destroy', $staff->staff_id) }}" method="POST" onsubmit="return confirm('Xoá nhân viên này?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Xoá"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-2x mb-2"></i><br>Chưa có nhân viên nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination Footer --}}
    @if($staffs->hasPages())
    <div class="bg-white border-top px-3 py-3 d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Hiển thị
            <span class="font-weight-bold">{{ $staffs->firstItem() }}</span> –
            <span class="font-weight-bold">{{ $staffs->lastItem() }}</span>
            trong tổng số
            <span class="font-weight-bold">{{ $staffs->total() }}</span> kết quả
        </div>
        <div>
            {{ $staffs->appends(request()->query())->onEachSide(1)->links() }}
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
    
    .service-image { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; }
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
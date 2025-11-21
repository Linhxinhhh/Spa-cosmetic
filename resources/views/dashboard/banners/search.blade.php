@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Kết quả tìm kiếm banner')
@section('page-title', 'Kết quả tìm kiếm banner')

@section('content')
<style>
    .table-modern {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: none;
    }
    .table-modern thead {
        background: linear-gradient(135deg,#1e40af 0%,#3b82f6 100%);
        color: white;
    }
    .table-modern thead th {
        border: none;
        padding: 1rem;
        font-weight: 600;
        text-align: center;
    }
    .table-modern tbody td {
        border: none;
        padding: 1rem;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }
    .table-modern tbody tr:hover {
        background-color:#eff6ff;
        transition: all 0.3s ease;
    }
    .brand-header {
        background: linear-gradient(135deg,#1e40af 0%,#3b82f6 100%);
        color:white;
        padding:2rem;
        border-radius:15px;
        margin-bottom:2rem;
        box-shadow:0 10px 25px rgba(30,64,175,0.2);
    }
    .banner-thumb {
        width: 120px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="brand-header">
        <h1 class="text-2xl font-bold mb-1">
            <i class="fas fa-search mr-2"></i>Kết quả tìm kiếm banner
        </h1>
        <p>Kết quả cho từ khóa: <strong>"{{ $keyword }}"</strong></p>
        <a href="{{ route('admin.banners.index') }}"
           class="text-white hover:underline hover:text-blue-600 transition-colors duration-200">
            ← Quay lại danh sách banner
        </a>
    </div>

    {{-- Kết quả --}}
    @if($results->count() === 0)
        <div class="p-6 bg-white rounded-lg shadow text-center text-gray-500">
            <i class="fas fa-inbox fa-2x mb-2"></i>
            <p>Không tìm thấy banner nào</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Hình ảnh</th>
                        <th>Link</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $banner)
                        <tr>
                            <td><strong>#{{ $banner->banner_id }}</strong></td>
                            <td class="text-left font-medium">{{ $banner->title }}</td>
                            <td>
                                <img src="{{ $banner->image ? asset('storage/'.$banner->image) : asset('images/placeholder-1x1.png') }}"
                                     class="banner-thumb" alt="{{ $banner->title }}">
                            </td>
                            <td>
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" class="text-blue-500 hover:underline">
                                        {{ $banner->link }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ optional($banner->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.banners.edit', $banner->banner_id) }}"
                                   class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner->banner_id) }}"
                                      method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa banner này không?')"
                                            class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6 flex justify-end">
            {{ $results->appends(request()->query())->links() }}
        </div>
    @endif

</div>
@endsection

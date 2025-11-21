@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị Banner')
@section('page-title', 'Danh mục Banner')

@section('content')
<div class="min-h-screen from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-image text-white text-lg"></i>
                Quản lý Banner
            </h1>
            <p class="page-subtitle">Theo dõi và quản lý thông tin banner của Lyn & Spa</p>
            <a href="{{ route('admin.banners.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:from-emerald-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Thêm Banner
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Search & Filter Bar -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <!-- Search Form -->
            <form action="{{ route('admin.banners.search') }}" method="GET" class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100">
                <input type="text" 
                       name="keyword" 
                       value="{{ request('keyword') }}"
                       placeholder="Tìm banner..." 
                       class="px-3 py-2 border border-gray-200 rounded-lg w-64 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-search mr-1"></i> Tìm kiếm
                </button>
            </form>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.banners.index') }}" class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100">
                @php $st = $filters['status'] ?? 'all'; @endphp
                <select name="status"
                        class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 w-48">
                    <option value="all" {{ $st === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                    <option value="1" {{ $st === '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ $st === '0' ? 'selected' : '' }}>Ngưng hoạt động</option>
                </select>

                <button type="submit"
                        class="px-5 py-2 bg-blue-50 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>

                <a href="{{ route('admin.banners.index') }}"
                   class="px-5 py-2 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    Xóa lọc
                </a>
            </form>
        </div>

        <!-- Banners Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-blue-200 table-container">
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-gradient-to-r from-blue-200 to-indigo-200 border-b border-blue-200 shadow-sm">
                        <tr>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Tiêu đề</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Hình ảnh</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Link</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Vị trí</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Bắt đầu</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Kết thúc</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($banners as $index => $banner)
                            <tr class="hover:bg-blue-100 hover:border-l-4 hover:border-blue-500 transition-colors duration-150 group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center text-white font-bold text-sm hover:scale-105 transition-transform duration-200">
                                            {{ strtoupper(substr($banner->title, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                <i class="fas fa-image text-cyan-500 mr-2"></i>
                                                {{ $banner->title }}
                                            </div>
                                            <div class="text-sm text-gray-500">ID: #{{ $banner->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($banner->image)
                                        @php
                                            $fullPath = storage_path('app/public/' . $banner->image);
                                            if (file_exists($fullPath)) {
                                                echo '<img src="' . asset('storage/' . $banner->image) . '" alt="' . $banner->title . '" width="50" class="img-thumbnail">';
                                            } else {
                                                echo '<span style="color: #ef4444;">File không tồn tại</span>';
                                            }
                                        @endphp
                                    @else
                                        <span>Không có hình ảnh</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ $banner->link }}" class="text-blue-600 hover:underline" target="_blank">{{ $banner->link }}</a>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ $banner->position }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $banner->status == 1 ? 'bg-emerald-200 text-emerald-900' : 'bg-red-200 text-red-900' }} badge-glow hover:shadow-md hover:bg-opacity-80 transition-all duration-200">
                                        <div class="w-2 h-2 {{ $banner->status == 1 ? 'bg-emerald-500' : 'bg-red-500' }} rounded-full mr-2"></div>
                                        {{ $banner->status == 1 ? 'Hoạt động' : 'Ngưng hoạt động' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ $banner->start_date }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ $banner->end_date }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.banners.edit', $banner->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200 hover:scale-105 transition-all duration-200 text-sm font-medium">
                                            <i class="fas fa-edit mr-1"></i>
                                            Sửa
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" 
                                              method="POST" style="display:inline">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa banner này?')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 hover:scale-105 transition-all duration-200 text-sm font-medium">
                                                <i class="fas fa-trash mr-1"></i>
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Hiển thị <span class="font-medium">{{ $banners->firstItem() }}</span> đến <span class="font-medium">{{ $banners->lastItem() }}</span> 
                        trong tổng số <span class="font-medium">{{ $banners->total() }}</span> kết quả
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ $banners->previousPageUrl() }}" 
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 {{ !$banners->onFirstPage() ?: 'disabled:opacity-50 disabled:cursor-not-allowed' }}">
                            <i class="fas fa-chevron-left mr-1"></i>
                            Trước
                        </a>
                        @foreach($banners->getUrlRange(1, $banners->lastPage()) as $page => $url)
                            <a href="{{ $url }}" 
                               class="px-3 py-2 text-sm font-medium {{ $banners->currentPage() == $page ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700' }} rounded-lg">
                                {{ $page }}
                            </a>
                        @endforeach
                        <a href="{{ $banners->nextPageUrl() }}" 
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 {{ !$banners->hasMorePages() ?: 'disabled:opacity-50 disabled:cursor-not-allowed' }}">
                            Sau
                            <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dependencies -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .container {
        max-width: none !important;
    }
    .page-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
    }
    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }
    /* Custom hover effects for rows */
    tr:hover {
        border-left: 4px solid #3b82f6; /* blue-500 */
    }
    /* Glowing effect for badges */
    .badge-glow {
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.3); /* blue-500 */
    }
    /* Table container glow */
    .table-container {
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15); /* blue-500 */
    }
    /* Custom scrollbar */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: white; /* blue-50 */
        border-radius: 3px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #3b82f6; /* blue-500 */
        border-radius: 3px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #2563eb; /* blue-600 */
    }
    .img-thumbnail {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 4px;
    }
</style>
<script>
document.querySelectorAll('select[name], input[type="date"]').forEach(el => {
    el.addEventListener('change', () => el.form.submit());
});
</script>
@endsection
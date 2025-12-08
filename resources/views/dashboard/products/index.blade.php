@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị sản phẩm')
@section('page-title', 'Sản phẩm')

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin/giaodien/css/style.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        {{-- Header Section --}}
        {{-- Header Section --}}
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2" style="font-size:2.5rem;font-weight:700;">
                        <i class="fas fa-boxes-stacked me-2"></i> Quản trị sản phẩm
                    </h1>
                    <p class="mb-0" style="font-size:1.1rem;opacity:.9;">
                        Quản lý toàn bộ sản phẩm trong hệ thống của bạn
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-md-end gap-2">
                        {{-- Xuất Excel --}}
                        <a href="" class="btn-excel">
                            <i class="fas fa-download"></i> Xuất Excel
                        </a>
                        {{-- Thêm mới --}}
                        <a href="{{ route('admin.products.create') }}" class="btn-add">
                            <i class="fas fa-plus me-1"></i> Thêm mới
                        </a>
                    </div>
                </div>
            </div>
        </div>


        {{-- Success Alert --}}
        @if(session('success'))
            <div class="alert alert-modern mb-4">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif
        {{-- tim kiem va loc san pham ---}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

            {{--Tìm kiếm sp--}}
            <form action="{{ route('admin.products.search') }}" method="GET"
                class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100">
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm sản phẩm..."
                    class="px-3 py-2 border border-gray-200 rounded-lg w-64 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-search mr-1"></i>
                </button>
            </form>
            <!-- Form Lọc Sản phẩm -->
            <form method="GET" action="{{ route('admin.products.index') }}"
                class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100">

                {{-- Trạng thái --}}
                <select name="status"
                    class="px-5 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition w-40">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang bán</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Ngưng bán</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Hết hàng</option>
                </select>

                {{-- Danh mục --}}
                <select name="category_name"
                    class="px-5 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition w-56">
                    <option value="">Tất cả danh mục </option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->category_name }}" {{ request('category_name') == $cat->category_name ? 'selected' : '' }}>
                            {{ $cat->category_name }}
                        </option>
                    @endforeach
                </select>

                {{-- Thương hiệu --}}
                <select name="brand_id"
                    class="px-5 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition w-56">
                    <option value="">Tất cả thương hiệu</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->brand_id }}" {{ request('brand_id') == $brand->brand_id ? 'selected' : '' }}>
                            {{ $brand->brand_name }}
                        </option>
                    @endforeach
                </select>

                {{-- Nút lọc --}}
                <button type="submit" style="margin-left: 20px;"
                    class="px-5 py-2 bg-blue-50 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>

                {{-- Xoá lọc --}}
                <a href="{{ route('admin.products.index') }}" style="margin-left: 10px"
                    class="px-5 py-2 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    Xoá lọc
                </a>
            </form>
        </div>

        {{-- Products Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <i class="fas fa-hashtag mr-1"></i>ID
                        </th>
                        <th style="width: 100px;">
                            <i class="fas fa-tag mr-1"></i>Tên sản phẩm
                        </th>
                        <th style="width: 200px;">
                            <i class="fas fa-layer-group mr-1"></i>Danh mục
                        </th>
                        <th style="width: 200px;">
                            <i class="fas fa-trademark mr-1"></i>Thương hiệu
                        </th>
                        <th style="width: 100px;">
                            <i class="fas fa-money-bill-wave mr-1"></i>Giá gốc
                        </th>
                        <th style="width: 110px;">
                            <i class="fas fa-percentage mr-1"></i>Giá bán
                        </th>
                        <th style="width: 70px;">
                            <i class="fas fa-cubes mr-1"></i>Tồn kho
                        </th>
                        <th style="width: 180px;">
                            <i class="fas fa-circle mr-1"></i>Trạng thái
                        </th>
                        <th style="width: 100px;">
                            <i class="fas fa-image mr-1"></i>Hình ảnh
                        </th>
                        <th style="width: 150px;">
                            <i class="fas fa-cogs mr-1"></i>Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td><strong>#{{ $product->product_id }}</strong></td>
                            <td style="text-align: left;">
                                <strong>{{ $product->product_name }}</strong>
                            </td>
                            <td>
                                <span class="badge"
                                    style=" color: #1e40af; padding: 6px 12px; border-radius: 20px; align-items: left;">
                                    {{ $product->category->category_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge"
                                    style="background: #f0f9ff; color: #0369a1; padding: 6px 12px; border-radius: 20px;">
                                    {{ $product->brand->brand_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="price-display">{{ number_format($product->price) }}đ</span>
                            </td>
                            <td>
                                @if($product->discount_percent > 0)
                                    <div class="discount-badge">-{{ $product->discount_percent }}%</div>
                                    <div class="discounted-price">
                                        {{ number_format($product->price - ($product->price * $product->discount_percent / 100)) }}đ
                                    </div>
                                @else
                                    <span class="text-muted">{{ number_format($product->price) }}đ</span>
                                @endif
                            </td>
                            <td>
                                <strong
                                    style="color: {{ $product->stock_quantity > 10 ? '#10b981' : ($product->stock_quantity > 0 ? '#f59e0b' : '#ef4444') }};">
                                    {{ $product->stock_quantity }}
                                </strong>
                            </td>
                            <td>
                                @if($product->status == 1 || $product->status == 'dang_ban')
                                    <span class="status-badge status-active">Đang bán</span>
                                @elseif($product->status == 2 || $product->status == 'ngung_ban')
                                    <span class="status-badge status-inactive">Ngưng bán</span>
                                @elseif($product->status == 3 || $product->status == 'het_hang')
                                    <span class="status-badge status-out-of-stock">Hết hàng</span>
                                @else
                                    <span class="text-muted">Không xác định</span>
                                @endif
                            </td>
                           <td class="text-center">
    @php
        // đã eager-load imagesRel(limit 2) trong controller
        $urls = $product->imagesRel->pluck('url')->all();
        // fallback: dùng accessor cũ nếu chưa có ảnh bảng product_images
        $fallback =$product->image_url;
        $firstUrl  = $urls[0] ?? null;
        $secondUrl = $urls[1] ?? null;

        $first  = $firstUrl ? src_img_get($firstUrl) : $fallback;
        $second = $secondUrl ? src_img_get($secondUrl) : null;

    @endphp

    <div class="thumb-wrap">
        <img class="img-primary" src="{{ $first }}" alt="{{ $product->product_name }}" loading="lazy">
        @if($second)
            <img class="img-secondary" src="{{ $second }}" alt="{{ $product->product_name }} (ảnh 2)" loading="lazy">
        @endif
    </div>
</td>



                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn btn-edit"
                                        title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST"
                                        style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')"
                                            title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div style="color: #6b7280; font-size: 1.1rem;">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <br>Chưa có sản phẩm nào
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Hiển thị <span class="font-medium">1</span> đến <span
                            class="font-medium">{{ $products->count() }}</span>
                        trong tổng số <span class="font-medium">{{ $products->count() }}</span> kết quả
                    </div>
                    <div class="flex items-center space-x-2">
                        <button
                            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            <i class="fas fa-chevron-left mr-1"></i>
                            Trước
                        </button>
                        <button
                            class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                            1
                        </button>
                        <button
                            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            Sau
                            <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>






    </div>

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

    @endpush
@endsection
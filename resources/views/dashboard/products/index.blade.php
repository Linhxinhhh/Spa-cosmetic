@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị sản phẩm')
@section('page-title', 'Sản phẩm')

@push('styles')
<style>
    .product-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(30, 64, 175, 0.2);
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
    .btn-primary-custom {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    }
    
    .table-modern {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: none;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
    }
    
    .table-modern thead th {
        border: none;
        padding: 1rem;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }
    
    .table-modern tbody td {
        border: none;
        padding: 1rem;
        vertical-align: middle;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .table-modern tbody tr:hover {
        background-color: white;
        transition: all 0.3s ease;
    }
    
    .status-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .status-inactive {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .status-out-of-stock {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .discount-badge {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-bottom: 5px;
        display: inline-block;
    }
    
    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .product-image:hover {
        transform: scale(1.1);
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }
    
    .btn-edit {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .btn-edit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .btn-delete:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }
    
    .alert-modern {
        border: none;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    
    .price-display {
        font-weight: 700;
        color: #1e40af;
        font-size: 1.1rem;
    }
    
    .discounted-price {
        color: #dc2626;
        font-weight: 700;
    }
     .thumb-wrap {
    width: 80px; height: 80px;           /* có thể tăng nếu muốn */
    border-radius: 10px;
    background:white;
    position: relative;
    overflow: hidden;
    margin: 0 auto;
  }
  .thumb-wrap img {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: contain;                  /* không méo, không phóng quá mức */
    transition: opacity .25s ease-in-out, transform .25s ease-in-out;
  }
  .thumb-wrap .img-primary { opacity: 1; }
  .thumb-wrap .img-secondary { opacity: 0; }
  .thumb-wrap:hover .img-primary { opacity: 0; }
  .thumb-wrap:hover .img-secondary { opacity: 1; transform: scale(1.02); }
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
    <form action="{{ route('admin.products.search') }}" method="GET" class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100">
        <input type="text" 
               name="keyword" 
               value="{{ request('keyword') }}"
               placeholder="Tìm sản phẩm..." 
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
        <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Đang bán</option>
        <option value="2" {{ request('status')=='2' ? 'selected' : '' }}>Ngưng bán</option>
        <option value="3" {{ request('status')=='3' ? 'selected' : '' }}>Hết hàng</option>
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
            <option value="{{ $brand->brand_id }}" {{ request('brand_id')==$brand->brand_id ? 'selected' : '' }}>
                {{ $brand->brand_name }}
            </option>
        @endforeach
    </select>

    {{-- Nút lọc --}}
     <button type="submit"
         style="margin-left: 20px;"   class="px-5 py-2 bg-blue-50 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition-colors duration-200">
            <i class="fas fa-filter mr-2"></i> Lọc
        </button>

    {{-- Xoá lọc --}}
    <a href="{{ route('admin.products.index') }}"
       style="margin-left: 10px" class="px-5 py-2 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors duration-200">
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
                            <span class="badge" style=" color: #1e40af; padding: 6px 12px; border-radius: 20px; align-items: left;">
                                {{ $product->category->category_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: #f0f9ff; color: #0369a1; padding: 6px 12px; border-radius: 20px;">
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
                            <strong style="color: {{ $product->stock_quantity > 10 ? '#10b981' : ($product->stock_quantity > 0 ? '#f59e0b' : '#ef4444') }};">
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
                       <td>
    @php
        // đã eager-load imagesRel(limit 2) trong controller
        $urls = $product->imagesRel->pluck('url')->all();
        // fallback: dùng accessor cũ nếu chưa có ảnh bảng product_images
        $fallback = $product->image_url ?? asset('images/no-image.png');
        $first  = $urls[0] ?? $fallback;
        $second = $urls[1] ?? null;
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
                                <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn btn-edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" style="display:inline">
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
                        Hiển thị <span class="font-medium">1</span> đến <span class="font-medium">{{ $products->count() }}</span> 
                        trong tổng số <span class="font-medium">{{ $products->count() }}</span> kết quả
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-chevron-left mr-1"></i>
                            Trước
                        </button>
                        <button class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                            1
                        </button>
                        <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
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
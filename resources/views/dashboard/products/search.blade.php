@extends('dashboard.layouts.app')

@section('page-title', 'Kết quả tìm kiếm sản phẩm')

@section('content')
<style>
    .product-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(30, 64, 175, 0.2);
    }
</style>



<div class="container-fluid">

    {{-- Header --}}
    <div class="product-header   ">
        <h1 class="text-2xl font-bold mb-1">
            <i class="fas fa-search mr-2"></i>Kết quả tìm kiếm sản phẩm
        </h1>
        <p>Kết quả cho từ khóa: <strong>"{{ $keyword }}"</strong></p>
        <a href="{{ route('admin.products.index') }}" 
       class="text-white items-right hover:underline hover:text-blue-600 transition-colors duration-200">
        ← Quay lại danh sách sản phẩm
    </a>
    </div>

    {{-- Kết quả --}}
    @if($results->count() === 0)
        <div class="p-6 bg-white rounded-lg shadow text-center text-gray-500">
            <i class="fas fa-inbox fa-2x mb-2"></i>
            <p>Không tìm thấy sản phẩm nào</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    {{-- Header --}}
                    <thead class="bg-blue-600">
                        <tr>
                            <th class="px-3 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-hashtag mr-1"></i>ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-tag mr-1"></i>Tên sản phẩm
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-list mr-1"></i>Danh mục
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-trademark mr-1"></i>Thương hiệu
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-money-bill mr-1"></i>Giá gốc
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-percent mr-1"></i>Giá bán
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-boxes mr-1"></i>Tồn kho
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-circle mr-1"></i>Trạng thái
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-image mr-1"></i>Hình ảnh
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                                <i class="fas fa-cog mr-1"></i>Thao tác
                            </th>
                        </tr>
                    </thead>
                    
                    {{-- Body --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($results as $product)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                {{-- ID --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-black">
                                    #{{ $product->product_id }}
                                </td>
                                
                                {{-- Tên sản phẩm --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-black">
                                        {{ $product->product_name }}
                                    </div>
                                </td>
                                
                                {{-- Danh mục --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-black">
                                        {{ $product->category->category_name ?? 'N/A' }}
                                    </span>
                                </td>
                                
                                {{-- Thương hiệu --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                                    {{ $product->brand->brand_name ?? 'N/A' }}
                                </td>
                                
                                {{-- Giá gốc --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium text-black">
                                        {{ number_format($product->price) }}đ
                                    </span>
                                </td>
                                
                                {{-- Giá bán (với giảm giá) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($product->discount_percent > 0)
                                        <div class="flex flex-col items-center">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-500 text-white mb-1">
                                                -{{ $product->discount_percent }}%
                                            </span>
                                            <span class="text-sm font-bold text-red-600">
                                                {{ number_format($product->price * (100 - $product->discount_percent) / 100) }}đ
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-sm text-black">{{ number_format($product->price) }}đ</span>
                                    @endif
                                </td>
                                
                                {{-- Tồn kho --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium text-black">{{ $product->stock_quantity }}</span>
                                </td>
                                
                                {{-- Trạng thái --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($product->status == 1)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                            ĐANG BÁN
                                        </span>
                                    @elseif($product->status == 2)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500 text-white">
                                            NGỪNG BÁN
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-500 text-white">
                                            HẾT HÀNG
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- Hình ảnh --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($product->images)
                                        <div class="flex justify-center">
                                            <img src="{{ asset($product->images) }}" class="w-16 h-16 object-cover rounded-lg border-2 border-gray-200 shadow-sm">
                                        </div>
                                    @else
                                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mx-auto border-2 border-gray-200">
                                            <i class="fas fa-image text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                </td>
                                
                                {{-- Thao tác --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('admin.products.edit', $product->product_id) }}" 
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md text-white bg-blue-500 hover:bg-blue-600 transition-colors duration-200">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->product_id) }}" 
                                              method="POST" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Xóa sản phẩm này?')" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md text-white bg-red-500 hover:bg-red-600 transition-colors duration-200">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $results->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
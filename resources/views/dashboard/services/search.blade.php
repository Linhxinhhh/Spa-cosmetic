@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Kết quả tìm kiếm dịch vụ')
@section('page-title', 'Kết quả tìm kiếm')
@section('content')

<style>
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
    }
    .table-modern tbody td {
        border: none;
        padding: 1rem;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
    }
    .table-modern tbody tr:hover {
        background-color: #eff6ff;
        transition: all 0.3s ease;
    }


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
       <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên dịch vụ</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $service)
                    <tr>
                        <td><strong>#{{ $service->service_id }}</strong></td>
                        <td class="text-left font-medium">{{ $service->service_name }}</td>
                        <td>
                            {{ $service->category->category_name ?? 'N/A' }}
                        </td>
                        <td>
                            {{ number_format($service->price) }} đ
                        </td>
                        <td>{{ $service->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.services.edit', $service->service_id) }}"
                               class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.services.destroy', $service->service_id) }}"
                                  method="POST"
                                  style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này không?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Không tìm thấy dịch vụ nào phù hợp
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6 flex justify-end">
        {{ $results->links() }}
    </div>



   

 
</div>
@endsection

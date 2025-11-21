@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị danh mục dịch vụ')
@section('page-title', 'Tìm kiếm danh mục dịch vụ')


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
<div class="p-6">
   <div class="product-header   ">
        <h1 class="text-2xl font-bold mb-1">
            <i class="fas fa-search mr-2"></i>Kết quả tìm kiếm danh mục dịch vụ
        </h1>
        <p>Kết quả cho từ khóa: <strong>"{{ $keyword }}"</strong></p>
        <a href="{{ route('admin.service_categories.index') }}" 
       class="text-white items-right hover:underline hover:text-blue-600 transition-colors duration-200">
        ← Quay lại 
    </a>
    </div>

  @if($results->count() === 0)
      <div class="p-6 bg-white rounded-lg shadow text-center text-gray-500">
            <i class="fas fa-inbox fa-2x mb-2"></i>
            <p>Không tìm thấy danh mục nào</p>
        </div>
  @else
    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full table-auto">
            <thead>
              <tr style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                <th class="p-3 text-left text-white font-semibold">Danh mục</th>
                <th class="p-3 text-left text-white font-semibold">Cấp độ</th>
                <th class="p-3 text-left text-white font-semibold">Trạng thái</th>
                <th class="p-3 text-left text-white font-semibold">Ngày tạo</th>
                <th class="p-3 text-center text-white font-semibold">Thao tác</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @foreach($results as $parent)
                <tr class="hover:bg-gray-50">
                  <td class="p-3 font-medium">
                    <i class="fas fa-folder text-cyan-500 mr-2"></i>
                    {{ $parent->category_name }} 
                    <span class="text-gray-500">#{{ $parent->category_id }}</span>
                  </td>
                  <td class="p-3">
                    <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs">
                      Cấp 1
                    </span>
                  </td>
                  <td class="p-3">
                    @if((int)$parent->status === 1)
                      <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs">
                        Hoạt động
                      </span>
                    @else
                      <span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs">
                        Ngưng hoạt động
                      </span>
                    @endif
                  </td>
                  <td class="p-3">
                    {{ optional($parent->created_at)->format('d/m/Y H:i') }}
                  </td>
                  <td class="p-3 text-center">
                    <a href="{{ route('admin.service_categories.edit', $parent->category_id) }}" 
                       class="text-blue-600 hover:underline">
                      Sửa
                    </a>
                  </td>
                </tr>

                {{-- Hiển thị toàn bộ con của cha --}}
                @if($parent->children && $parent->children->count())
                  @foreach($parent->children as $child)
                    <tr class="bg-gray-50 hover:bg-gray-100">
                      <td class="p-3 pl-10">
                        <i class="fas fa-folder-open text-teal-500 mr-2"></i>
                        {{ $child->category_name }} 
                        <span class="text-gray-500">#{{ $child->category_id }}</span>
                      </td>
                      <td class="p-3">
                        <span class="px-2 py-1 rounded bg-teal-100 text-teal-800 text-xs">
                          Cấp 2
                        </span>
                      </td>
                      <td class="p-3">
                        @if((int)$child->status === 1)
                          <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs">
                            Hoạt động
                          </span>
                        @else
                          <span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs">
                            Ngưng hoạt động
                          </span>
                        @endif
                      </td>
                      <td class="p-3">
                        {{ optional($child->created_at)->format('d/m/Y H:i') }}
                      </td>
                      <td class="p-3 text-center">
                        <a href="{{ route('admin.service_categories.edit', $child->category_id) }}" 
                           class="text-blue-600 hover:underline">
                          Sửa
                        </a>
                      </td>
                    </tr>
                  @endforeach
                @endif

              @endforeach
            </tbody>
          </table>
        </div>

        <div class="px-4 py-3 border-t">
          {{ $results->links() }}
        </div>
      </div>
    @endif
  </div>
</div>

@endsection
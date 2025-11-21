@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị danh mục dịch vụ')
@section('page-title', 'Danh mục dịch vụ')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header Section -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-concierge-bell text-white text-lg"></i>
                Quản lý danh mục dịch vụ
            </h1>
            <p class="page-subtitle">Quản lý và tổ chức các danh mục dịch vụ của Lyn & Spa</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <button class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Xuất Excel
                </button>
                <a href="{{ route('admin.service_categories.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:from-emerald-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Thêm mới
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-list text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng danh mục</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $categories->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6 border border-emerald-100">
               
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đang hoạt động</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $categories->where('status', 1)->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6 border border-orange-100">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-sitemap text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Danh mục con</p>
                        <p class="text-2xl font-bold text-gray-900">
                            @php
                                $childrenCount = $categories->sum(function($category) {
                                    return $category->children ? $category->children->count() : 0;
                                });
                            @endphp
                            {{ $childrenCount }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

      
        <!-- Search & Filter Bar -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
   

    <!-- Form Tìm kiếm (bên trái) -->
    <form action="{{ route('admin.service_categories.search') }}" method="GET" class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100">
        <input type="text" 
               name="keyword" 
               value="{{ request('keyword') }}"
               placeholder="Tìm danh mục..." 
               class="px-3 py-2 border border-gray-200 rounded-lg w-64 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
        <button type="submit" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
            <i class="fas fa-search mr-1"></i> Tìm kiếm
        </button>
    </form>
    <!-- Form Lọc (bên phải) -->
    <form method="GET" action="{{ route('admin.service_categories.index') }}" class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100">
        @php $st = $filters['status'] ?? 'all'; @endphp
        <select name="status"
                class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 w-48">
            <option value="all" {{ $st==='all'?'selected':'' }}>Tất cả trạng thái</option>
            <option value="hoạt động" {{ $st==='hoạt động'?'selected':'' }}>Hoạt động</option>
            <option value="ngưng hoạt động" {{ $st==='ngưng hoạt động'?'selected':'' }}>Ngưng hoạt động</option>
        </select>

        @php $lv = $filters['level'] ?? '1'; @endphp
        <select name="level"
                class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 w-28">
            <option value="1"   {{ $lv==='1'?'selected':'' }}>Cấp 1</option>
            <option value="2"   {{ $lv==='2'?'selected':'' }}>Cấp 2</option>
            <option value="all" {{ $lv==='all'?'selected':'' }}>Mọi cấp</option>
        </select>

        <button type="submit"
            class="px-5 py-2 bg-blue-50 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition-colors duration-200">
            <i class="fas fa-filter mr-2"></i> Lọc
        </button>

        <a href="{{ route('admin.product_categories.index') }}"
           class="px-5 py-2 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors duration-200">
            Xoá lọc
        </a>
    </form>
    </div>

    


        <!-- Categories Table -->
       <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-blue-200 table-container">
  <div class="overflow-x-auto">
    <table class="w-full table-auto border-collapse">
      <thead class="bg-gradient-to-r from-blue-200 to-indigo-200 border-b border-blue-200 shadow-sm">
        <tr>
          <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-20">Hình ảnh</th>
          <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Danh mục</th>
          <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cấp độ</th>
          <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Trạng thái</th>
          <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dịch vụ</th>
          <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ngày tạo</th>
          <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Thao tác</th>
        </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($categories as $category)
          {{-- ===== ROW CHA ===== --}}
          @php
            $totalServices = ($category->services_count ?? 0) + ($category->children?->sum('services_count') ?? 0);
          @endphp
          <tr class="hover:bg-blue-50 transition-colors duration-150">
            <td class="px-6 py-4">
              <img
                src="{{ $category->image ? asset('storage/'.$category->image) : asset('images/default-category.png') }}"
                alt="{{ $category->category_name }}"
                class="w-12 h-12 p-1.5 rounded-lg object-contain bg-gray-50 ring-1 ring-gray-200"
              >
            </td>

            <td class="px-6 py-4">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                  {{ strtoupper(substr($category->category_name, 0, 2)) }}
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900 flex items-center">
                    <i class="fas fa-folder text-cyan-500 mr-2"></i>{{ $category->category_name }}
                  </div>
                  <div class="text-xs text-gray-500">ID: #{{ $category->category_id }}</div>
                </div>
              </div>
            </td>

            <td class="px-6 py-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-200 text-blue-900">
                <i class="fas fa-layer-group mr-1"></i> Cấp 1
              </span>
            </td>

            <td class="px-6 py-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $category->status ? 'bg-emerald-200 text-emerald-900' : 'bg-red-200 text-red-900' }}">
                <span class="w-2 h-2 rounded-full mr-2 {{ $category->status ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                {{ $category->status ? 'Hoạt động' : 'Ẩn' }}
              </span>
            </td>

            <td class="px-6 py-4">
              <div class="flex items-center text-sm text-gray-900">
                <i class="fas fa-concierge-bell text-gray-400 mr-2"></i>
                <span class="font-medium">{{ $totalServices }}</span>
                <span class="text-gray-500 ml-1">dịch vụ</span>
              </div>
            </td>

            <td class="px-6 py-4">
              <div class="text-sm text-gray-900">{{ optional($category->created_at)->format('d/m/Y') }}</div>
              <div class="text-sm text-gray-500">{{ optional($category->created_at)->format('H:i') }}</div>
            </td>

            <td class="px-6 py-4 text-right">
              <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.service_categories.edit', $category->category_id) }}"
                   class="inline-flex items-center px-3 py-2 bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200 transition">
                  <i class="fas fa-edit mr-1"></i> Sửa
                </a>
                <form action="{{ route('admin.service_categories.destroy', $category->category_id) }}" method="POST" onsubmit="return confirm('Xoá danh mục này?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition">
                    <i class="fas fa-trash mr-1"></i> Xoá
                  </button>
                </form>
              </div>
            </td>
          </tr>

          {{-- ===== CÁC ROW CON CỦA CHA NÀY ===== --}}
          @if($category->children && $category->children->count())
            @foreach($category->children as $child)
              <tr class="bg-gray-50 hover:bg-blue-50 transition-colors duration-150">
                <td class="px-6 py-4">
                  <img
                    src="{{ $child->image ? asset('storage/'.$child->image) : asset('images/default-category.png') }}"
                    alt="{{ $child->category_name }}"
                    class="w-12 h-12 p-1.5 rounded-lg object-contain bg-gray-50 ring-1 ring-gray-200"
                  >
                </td>

                <td class="px-6 py-4">
                  <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-3 ml-6">
                      <div class="w-2 h-8 border-l-2 border-b-2 border-gray-300 rounded-bl-lg"></div>
                      <div class="w-8 h-8 bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr($child->category_name, 0, 2)) }}
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900 flex items-center">
                          <i class="fas fa-folder-open text-teal-500 mr-2"></i>{{ $child->category_name }}
                        </div>
                        <div class="text-xs text-gray-500">ID: #{{ $child->category_id }}</div>
                      </div>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-teal-200 text-teal-900">
                    <i class="fas fa-layer-group mr-1"></i> Cấp 2
                  </span>
                </td>

                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $child->status ? 'bg-emerald-200 text-emerald-900' : 'bg-red-200 text-red-900' }}">
                    <span class="w-2 h-2 rounded-full mr-2 {{ $child->status ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    {{ $child->status ? 'Hoạt động' : 'Ẩn' }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <div class="flex items-center text-sm text-gray-900">
                    <i class="fas fa-concierge-bell text-gray-400 mr-2"></i>
                    <span class="font-medium">{{ $child->services_count ?? 0 }}</span>
                    <span class="text-gray-500 ml-1">dịch vụ</span>
                  </div>
                </td>

                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ optional($child->created_at)->format('d/m/Y') }}</div>
                  <div class="text-sm text-gray-500">{{ optional($child->created_at)->format('H:i') }}</div>
                </td>

                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <a href="{{ route('admin.service_categories.edit', $child->category_id) }}"
                       class="inline-flex items-center px-3 py-2 bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200 transition">
                      <i class="fas fa-edit mr-1"></i> Sửa
                    </a>
                    <form action="{{ route('admin.service_categories.destroy', $child->category_id) }}" method="POST" onsubmit="return confirm('Xoá danh mục này?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition">
                        <i class="fas fa-trash mr-1"></i> Xoá
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          @endif
        @empty
          <tr>
            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
              <i class="fas fa-inbox fa-2x mb-4"></i>
              <p>Không có danh mục nào</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Pagination (nếu cần) --}}
    <div class="bg-white border-t border-gray-200 px-6 py-4">
      {{ $categories->links() }}
    </div>
  </div>
</div>


        <!-- Bulk Actions -->
        
    </div>
</div>

<!-- Dependencies -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
  <!-- Pagination -->

<style>
    .container {
        max-width: none !important;
    }
    
    /* Page Header */
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
    
    /* Checkbox hover glow */
    input[type="checkbox"].hover\:shadow-cyan:hover {
        box-shadow: 0 0 6px rgba(6, 182, 212, 0.5); /* cyan-500 */
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
</style>

<script>
function toggleDropdown(categoryId) {
    const dropdown = document.getElementById(`dropdown-${categoryId}`);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(dd => {
        if (dd.id !== `dropdown-${categoryId}`) {
            dd.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleDropdown"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});
</script>
@endsection
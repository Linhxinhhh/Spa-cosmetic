
@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị danh mục sản phẩm')
@section('page-title', 'Danh mục sản phẩm')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm">
                <li>
                    <a href="{{ route('admin.product_categories.index') }}" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                        <i class="fas fa-home mr-1"></i>
                        Quản lý danh mục
                    </a>
                </li>
                <li class="text-gray-400">
                    <i class="fas fa-chevron-right"></i>
                </li>
                <li class="text-gray-600 font-medium">Thêm danh mục mới</li>
            </ol>
        </nav>

        <div class="max-w-3xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl shadow-xl mb-8 p-8 border border-blue-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Thêm danh mục sản phẩm</h1>
                        <p class="text-gray-600 mt-1">Tạo danh mục mới để tổ chức sản phẩm của bạn</p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 rounded-xl p-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ date('d/m/Y') }}</div>
                        <div class="text-sm text-gray-600">Ngày tạo</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-emerald-600">Mới</div>
                        <div class="text-sm text-gray-600">Trạng thái</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            @isset($parents)
                                {{ count($parents) }}
                            @else
                                0
                            @endisset
                        </div>
                        <div class="text-sm text-gray-600">Danh mục cha có sẵn</div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100">
                <div class="p-8">
                    <form action="{{ route('admin.product_categories.store') }}" method="POST" class="space-y-6"
                         enctype="multipart/form-data">
                        @csrf

                        <!-- Category Name -->
                        <div class="space-y-2">
                            <label for="category_name" class="flex items-center text-sm font-semibold text-gray-700">
                                <i class="fas fa-tag text-blue-500 mr-2"></i>
                                Tên danh mục sản phẩm
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" 
                                   id="category_name"
                                   name="category_name" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('category_name') border-red-300 @enderror" 
                                   value="{{ old('category_name') }}" 
                                   placeholder="Ví dụ: Sửa rữa mặt, Mặt nạ ..."
                                   required>
                            @error('category_name')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-xs text-gray-500">Tên danh mục sẽ hiển thị công khai cho khách hàng</p>
                        </div>

                        <!-- Parent Category -->
                        <div class="space-y-2">
                            <label for="parent_id" class="flex items-center text-sm font-semibold text-gray-700">
                                <i class="fas fa-sitemap text-blue-500 mr-2"></i>
                                Danh mục cha
                            </label>
                            <select name="parent_id" 
                                    id="parent_id" 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('parent_id') border-red-300 @enderror">
                                <option value="">🏠 Không có (danh mục gốc)</option>
                                
                                @isset($parents)
                                    @php
                                        function renderOptions($categories, $prefix = '') {
                                            foreach ($categories as $category) {
                                                $icon = $prefix ? '└─' : '📁';
                                                echo '<option value="'.$category->category_id.'">'.$icon.' '.$prefix.' '.$category->category_name.'</option>';
                                                if ($category->children && $category->children->count()) {
                                                    renderOptions($category->children, $prefix.'──');
                                                }
                                            }
                                        }
                                    @endphp
                                    @php renderOptions($parents); @endphp
                                @endisset
                            </select>
                            @error('parent_id')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-xs text-gray-500">Chọn danh mục cha để tạo cây phân cấp. Để trống nếu là danh mục gốc</p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label for="description" class="flex items-center text-sm font-semibold text-gray-700">
                                <i class="fas fa-align-left text-blue-500 mr-2"></i>
                                Mô tả danh mục
                            </label>
                            <textarea id="description"
                                      name="description" 
                                      rows="4"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 resize-none @error('description') border-red-300 @enderror" 
                                      placeholder="Nhập mô tả chi tiết về danh mục này...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-xs text-gray-500">Mô tả giúp khách hàng hiểu rõ hơn về danh mục này (không bắt buộc)</p>
                        </div>

                
                        <div class="space-y-2">
                            <label for="image" class="flex items-center text-sm font-semibold text-gray-700">
                                <i class="fas fa-image text-blue-500 mr-2"></i>
                                Ảnh danh mục
                            </label>
                            <input type="file" 
                                id="image" 
                                name="image" 
                                accept="image/*"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 
                                        focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 
                                        @error('image') border-red-300 @enderror">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-xs text-gray-500">Chọn ảnh đại diện cho danh mục (không bắt buộc)</p>
                        </div>
                       <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <!-- Nút tạo danh mục mới -->
                  <button type="submit"
                                    class="bg-blue-600 flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 
                                           text-white font-semibold rounded-xl shadow-lg hover:from-emerald-600 hover:to-teal-600 
                                           focus:outline-none focus:ring-4 focus:ring-emerald-300 transform hover:scale-105 
                                           transition-all duration-200">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Tạo danh mục mới
                            </button>

    <!-- Nút đặt lại form -->
    <button type="button" 
        onclick="document.querySelector('form').reset()"
        class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 
               bg-gray-100 text-gray-700 font-semibold rounded-xl 
               hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-300 
               transition-all duration-200">
        <i class="fas fa-undo mr-2"></i>
        Đặt lại form
    </button>

    <!-- Nút quay lại -->
    <a href="{{ route('admin.product_categories.index') }}" 
        class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 
               bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-200 
               hover:bg-gray-100 hover:border-gray-300 
               focus:outline-none focus:ring-4 focus:ring-gray-300 
               transition-all duration-200">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại danh sách
    </a>
</div>

                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="bg-emerald-50 rounded-xl p-6 mt-8 border border-emerald-100">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center mr-4 mt-1">
                        <i class="fas fa-question-circle text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-emerald-800 mb-2">Hướng dẫn tạo danh mục</h3>
                        <ul class="text-emerald-700 space-y-1 text-sm">
                            <li>• <strong>Danh mục gốc:</strong> Để trống "Danh mục cha" để tạo danh mục cấp 1</li>
                            <li>• <strong>Danh mục con:</strong> Chọn danh mục cha để tạo phân cấp</li>
                            <li>• <strong>Tên danh mục:</strong> Nên ngắn gọn, dễ hiểu và SEO friendly</li>
                            <li>• <strong>Trạng thái:</strong> Chọn "Ẩn" nếu chưa sẵn sàng hiển thị</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatusStyle() {
    const radios = document.querySelectorAll('input[name="status"]');
    radios.forEach(radio => {
        const label = radio.closest('label');
        if (radio.checked) {
            label.classList.add('border-blue-400', 'bg-blue-50');
            label.classList.remove('border-gray-200');
        } else {
            label.classList.remove('border-blue-400', 'bg-blue-50');
            label.classList.add('border-gray-200');
        }
    });
}

// Initialize status styling on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStatusStyle();
});
</script>
<script>
function syncStatusStyles() {
    const radios = document.querySelectorAll('input[name="status"]');
    radios.forEach(radio => {
        const label = radio.closest('label');
        // reset
        label.classList.remove(
            'border-blue-400','bg-blue-50','shadow-md',
            'border-emerald-400','bg-emerald-50','ring-1','ring-emerald-200',
            'border-red-400','bg-red-50','ring-red-200'
        );
        label.classList.add('border-gray-200','bg-white');

        if (radio.checked) {
            label.classList.remove('border-gray-200','bg-white');
            if (radio.value === '1') {
                // Hiển thị công khai -> xanh lá
                label.classList.add('border-emerald-400','bg-emerald-50','ring-1','ring-emerald-200','shadow-md');
            } else {
                // Ẩn tạm thời -> đỏ
                label.classList.add('border-red-400','bg-red-50','ring-1','ring-red-200','shadow-md');
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Lắng nghe thay đổi cho cả group (không cần onchange inline)
    document.querySelectorAll('input[name="status"]').forEach(r => {
        r.addEventListener('change', syncStatusStyles);
    });
    // Khởi tạo theo checked hiện tại (hỗ trợ old('status'))
    syncStatusStyles();
});
</script>


@endsection


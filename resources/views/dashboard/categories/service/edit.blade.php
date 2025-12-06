@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị danh mục dịch vụ')
@section('page-title', 'Danh mục dịch vụ')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm">
                <li>
                    <a href="{{ route('admin.service_categories.index') }}" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                        <i class="fas fa-home mr-1"></i>
                        Quản lý danh mục
                    </a>
                </li>
                <li class="text-gray-400">
                    <i class="fas fa-chevron-right"></i>
                </li>
                <li class="text-gray-600 font-medium">Sửa danh mục</li>
            </ol>
        </nav>

        <div class="max-w-3xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl shadow-xl mb-8 p-8 border border-blue-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Sửa danh mục dịch vụ</h1>
                        <p class="text-gray-600 mt-1">Chỉnh sửa thông tin danh mục "{{ $category->category_name }}"</p>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 rounded-xl p-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">#{{ $category->category_id }}</div>
                        <div class="text-sm text-gray-600">ID danh mục</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-emerald-600">{{ date('d/m/Y') }}</div>
                        <div class="text-sm text-gray-600">Ngày sửa</div>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center">
                            <div class="w-3 h-3 bg-{{ $category->status ? 'emerald' : 'red' }}-500 rounded-full mr-2"></div>
                            <div class="text-2xl font-bold text-gray-800">{{ $category->status ? 'Hoạt động' : 'Ngưng hoạt động' }}</div>
                        </div>
                        <div class="text-sm text-gray-600">Trạng thái hiện tại</div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100">
                <div class="p-8">
                    <form action="{{ route('admin.service_categories.update', $category->category_id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf 
                        @method('PUT')

                        <!-- Category Name -->
                        <div class="space-y-2">
                            <label for="category_name" class="flex items-center text-sm font-semibold text-gray-700">
                                <i class="fas fa-concierge-bell text-blue-500 mr-2"></i>
                                Tên danh mục
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" 
                                   id="category_name"
                                   name="category_name" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('category_name') border-red-300 @enderror" 
                                   value="{{ old('category_name', $category->category_name) }}" 
                                   placeholder="Nhập tên danh mục dịch vụ"
                                   required>
                            @error('category_name')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-xs text-gray-500">Tên danh mục sẽ hiển thị công khai cho khách hàng</p>
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
                                      placeholder="Nhập mô tả chi tiết về danh mục...">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-xs text-gray-500">Mô tả giúp khách hàng hiểu rõ hơn về danh mục này</p>
                        </div>
                          <div>
                        <label class="block text-sm font-medium">Ảnh danh mục</label>
                        <div class="flex items-center gap-4">
                       <img src="{{ src_img_get($category->image) }}"
     class="w-12 h-12 p-1.5 rounded-lg object-contain bg-gray-50 ring-1 ring-gray-200" 
     alt="{{ $category->category_name }}">

                           
                        <input type="file" name="image" accept="image/*" class="input">
                        </div>
                        @if($category->image)
                        <label class="mt-2 inline-flex items-center gap-2">
                            <input type="checkbox" name="remove_image" value="1" class="rounded"> Xoá ảnh hiện tại
                        </label>
                        @endif
                        @error('image') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>
                        <!-- Status -->
                        <div class="space-y-2">
                            <label for="status" class="flex items-center text-sm font-semibold text-gray-700">
                                <i class="fas fa-toggle-on text-blue-500 mr-2"></i>
                                Trạng thái hiển thị
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select id="status"
                                    name="status" 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('status') border-red-300 @enderror">
                                <option value="1" {{ old('status', $category->status) == '1' ? 'selected' : '' }}>
                                    🟢 Hiển thị - Khách hàng có thể xem
                                </option>
                                <option value="0" {{ old('status', $category->status) == '0' ? 'selected' : '' }}>
                                    🔴 Ẩn - Chỉ admin có thể xem
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transform hover:scale-105 transition-all duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Cập nhật danh mục
                            </button>
                            <a href="{{ route('admin.service_categories.index') }}" 
                               class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-all duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Quay lại danh sách
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="bg-blue-50 rounded-xl p-6 mt-8 border border-blue-100">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-4 mt-1">
                        <i class="fas fa-lightbulb text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">Mẹo sử dụng</h3>
                        <ul class="text-blue-700 space-y-1 text-sm">
                            <li>• Tên danh mục nên ngắn gọn và dễ hiểu</li>
                            <li>• Mô tả chi tiết giúp khách hàng tìm kiếm dịch vụ dễ dàng hơn</li>
                            <li>• Chỉ hiển thị danh mục khi đã có dịch vụ</li>
                            <li>• Có thể chỉnh sửa thông tin bất cứ lúc nào</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
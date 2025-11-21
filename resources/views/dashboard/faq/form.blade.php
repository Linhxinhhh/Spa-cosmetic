@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản lý FAQ')
@section('page-title', $faq->exists ? 'Sửa FAQ' : 'Thêm FAQ')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
  <div class="container mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="mb-8">
      <ol class="flex items-center space-x-2 text-sm">
        <li>
          <a href="{{ route('admin.faqs.index') }}"
             class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
            <i class="fas fa-home mr-1"></i> Quản lý FAQ
          </a>
        </li>
        <li class="text-gray-400"><i class="fas fa-chevron-right"></i></li>
        <li class="text-gray-600 font-medium">{{ $faq->exists ? 'Sửa FAQ' : 'Thêm FAQ' }}</li>
      </ol>
    </nav>

    <div class="max-w-4xl mx-auto">

      {{-- Header Card --}}
      <div class="bg-white rounded-2xl shadow-xl mb-8 p-8 border border-blue-100">
        <div class="flex items-center mb-6">
          <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mr-4">
            <i class="fas fa-pen-to-square text-white text-xl"></i>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">
              {{ $faq->exists ? 'Chỉnh sửa Hỏi đáp' : 'Thêm Hỏi đáp mới' }}
            </h1>
            <p class="text-gray-600 mt-1">
              {{ $faq->exists ? ('#'.$faq->id.' — Cập nhật nội dung câu hỏi & trả lời') : 'Nhập thông tin câu hỏi & trả lời' }}
            </p>
          </div>
        </div>

        {{-- Quick Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 rounded-xl p-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $faq->exists ? '#'.$faq->id : 'Mới' }}</div>
            <div class="text-sm text-gray-600">Mã FAQ</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-emerald-600">{{ now()->format('d/m/Y') }}</div>
            <div class="text-sm text-gray-600">Ngày {{ $faq->exists ? 'sửa' : 'tạo' }}</div>
          </div>
          <div class="text-center">
            @php $isPub = old('status', $faq->status) === 'Xuất bản'; @endphp
            <div class="flex items-center justify-center">
              <div class="w-3 h-3 rounded-full mr-2 {{ $isPub ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
              <div class="text-2xl font-bold text-gray-800">{{ $isPub ? 'Xuất bản' : 'Bản nháp' }}</div>
            </div>
            <div class="text-sm text-gray-600">Trạng thái hiện tại</div>
          </div>
        </div>
      </div>

      {{-- Alerts --}}
      @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-6">
          @foreach($errors->all() as $e)
            <div class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i>{{ $e }}</div>
          @endforeach
        </div>
      @endif
      @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 mb-6">
          {{ session('success') }}
        </div>
      @endif

      {{-- Form Card --}}
   {{-- Form Card --}}
<div class="bg-white rounded-2xl shadow-xl border border-blue-100">
  <div class="p-8">
    <form method="POST"
          action="{{ $faq->exists ? route('admin.faqs.update',$faq) : route('admin.faqs.store') }}"
          enctype="multipart/form-data"   {{-- <<<<<< BẮT BUỘC để upload file --}}
          class="space-y-6">
      @csrf
      @if($faq->exists) @method('PUT') @endif

      {{-- Câu hỏi --}}
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <i class="fas fa-circle-question text-blue-500 mr-2"></i>
          Câu hỏi <span class="text-red-500 ml-1">*</span>
        </label>
        <input name="question"
               value="{{ old('question',$faq->question) }}"
               required
               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('question') border-red-300 @enderror"
               placeholder="Nhập câu hỏi">
        @error('question')
          <p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
      </div>

      {{-- Trả lời --}}
      <div class="space-y-2">
        <label class="flex items-center text-sm font-semibold text-gray-700">
          <i class="fas fa-comment-dots text-blue-500 mr-2"></i>
          Trả lời <span class="text-red-500 ml-1">*</span>
        </label>
        <textarea name="answer" rows="6" required
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 resize-y @error('answer') border-red-300 @enderror"
                  placeholder="Nhập câu trả lời chi tiết...">{{ old('answer',$faq->answer) }}</textarea>
        @error('answer')
          <p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Ảnh bìa --}}
        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <i class="fas fa-image text-blue-500 mr-2"></i> Ảnh bìa (tùy chọn)
          </label>

          <div class="flex items-center gap-4">
            <div class="w-28 h-28 rounded-xl overflow-hidden ring-1 ring-gray-200 bg-gray-50">
              <img id="coverPreview"
                   src="{{ $faq->cover_image ? asset('storage/'.$faq->cover_image) : 'https://via.placeholder.com/200x200?text=Cover' }}"
                   alt="cover"
                   class="w-full h-full object-cover">
            </div>

            <div class="flex-1">
              <input type="file" name="cover" id="cover" accept="image/*"
                     class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('cover') border-red-300 @enderror">
              @error('cover')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                  <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                </p>
              @enderror

              @if($faq->cover_image)
                <label class="mt-3 inline-flex items-center gap-2 text-sm text-gray-700">
                  <input type="checkbox" name="remove_cover" value="1" class="rounded border-gray-300">
                  Xoá ảnh bìa hiện tại
                </label>
              @endif

              <p class="text-xs text-gray-500 mt-2">Hỗ trợ JPG, JPEG, PNG, WEBP (tối đa 2MB). Tỉ lệ gợi ý 16:9.</p>
            </div>
          </div>
        </div>

        {{-- Chuyên mục --}}
        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <i class="fas fa-folder-open text-blue-500 mr-2"></i> Chuyên mục
          </label>
          <input name="category"
                 value="{{ old('category',$faq->category) }}"
                 class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('category') border-red-300 @enderror"
                 placeholder="VD: Đặt lịch, Thanh toán...">
          @error('category')
            <p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
          @enderror
        </div>

        {{-- Danh mục con --}}
        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <i class="fas fa-tags text-blue-500 mr-2"></i> Danh mục con
            <span class="text-xs text-gray-500 ml-2">(gõ để tạo mới)</span>
          </label>
          <input name="subcategory" list="faq-subcategories"
                 value="{{ old('subcategory',$faq->subcategory) }}"
                 class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('subcategory') border-red-300 @enderror"
                 placeholder="VD: Chăm sóc da, Chăm sóc tóc, Spa">
          <datalist id="faq-subcategories">
            @foreach(($subcategories ?? []) as $sc)
              <option value="{{ $sc }}"></option>
            @endforeach
          </datalist>
          @error('subcategory')
            <p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
          @enderror
        </div>

        {{-- Thứ tự --}}
        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <i class="fas fa-sort-numeric-down text-blue-500 mr-2"></i> Thứ tự
          </label>
          <input type="number" min="0" name="sort_order"
                 value="{{ old('sort_order',$faq->sort_order) }}"
                 class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('sort_order') border-red-300 @enderror"
                 placeholder="0">
          @error('sort_order')
            <p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
          @enderror
        </div>

        {{-- Trạng thái --}}
        <div class="space-y-2">
          <label class="flex items-center text-sm font-semibold text-gray-700">
            <i class="fas fa-toggle-on text-blue-500 mr-2"></i> Trạng thái
          </label>
          <select name="status"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-200 @error('status') border-red-300 @enderror">
            <option value="Xuất bản" {{ old('status',$faq->status)==='Xuất bản' ? 'selected' : '' }}>🟢 Xuất bản</option>
            <option value="Bản nháp" {{ old('status',$faq->status)==='Bản nháp' ? 'selected' : '' }}>🔴 Bản nháp</option>
          </select>
          @error('status')
            <p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
          @enderror
        </div>
      </div>

      {{-- Actions --}}
      <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
        <button class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transform hover:scale-105 transition-all duration-200">
          <i class="fas fa-save mr-2"></i> Lưu
        </button>
        <a href="{{ route('admin.faqs.index') }}"
           class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-all duration-200">
          <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
      </div>
    </form>
  </div>
</div>




    </div>
  </div>
</div>

{{-- Nếu layout chưa có sẵn, thêm assets sau (có thể bỏ nếu đã include ở layout) --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

@push('scripts')
<script>
document.getElementById('cover')?.addEventListener('change', function (e) {
  const file = e.target.files?.[0]; if (!file) return;
  const reader = new FileReader();
  reader.onload = (evt) => document.getElementById('coverPreview').src = evt.target.result;
  reader.readAsDataURL(file);
});
</script>
<script src="https://cdn.tailwindcss.com"></script>
@endpush
@endsection

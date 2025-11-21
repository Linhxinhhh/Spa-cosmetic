@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản lý FAQ')
@section('page-title', 'FAQ')

@section('content')
<div class="min-h-screen from-slate-50 to-blue-50">
  <div class="container mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="page-header">
      <h1 class="page-title">
        <i class="fas fa-circle-question text-white text-lg"></i>
        Quản lý FAQ
      </h1>
      <p class="page-subtitle">Tạo, sắp xếp và xuất bản câu hỏi thường gặp cho Lyn &amp; Spa</p>

      <div class="mt-4 flex gap-3">
        {{-- (tuỳ chọn) Nút xuất Excel --}}
        <a href="#" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200">
          <i class="fas fa-download mr-2"></i> Xuất Excel
        </a>

        {{-- Thêm FAQ --}}
        <a href="{{ route('admin.faqs.create') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:from-emerald-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200">
          <i class="fas fa-plus mr-2"></i> Thêm FAQ
        </a>
      </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
      <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 mb-6">
        {{ session('success') }}
      </div>
    @endif

    {{-- Stats cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-question text-blue-600 text-xl"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tổng FAQ</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? $faqs->total() }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6 border border-emerald-100">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Đã xuất bản</p>
            <p class="text-2xl font-bold text-gray-900">
              {{ $stats['published'] ?? collect($faqs->items())->where('status','Xuất bản')->count() }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6 border border-orange-100">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-file-lines text-orange-600 text-xl"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Bản nháp</p>
            <p class="text-2xl font-bold text-gray-900">
              {{ $stats['draft'] ?? collect($faqs->items())->where('status','Bản nháp')->count() }}
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- Search & Filter --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <form method="GET" action="{{ route('admin.faqs.index') }}"
            class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100 w-full md:w-auto">
        <input class="px-3 py-2 border border-gray-200 rounded-lg w-72 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
               name="q"
               value="{{ request('q', $q ?? '') }}"
               placeholder="Tìm câu hỏi / nội dung / chuyên mục">
        <select name="status"
                class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 w-48">
          {{-- Sửa selected: so sánh đúng với 'Xuất bản' / 'Bản nháp' --}}
          @php $st = request('status', $status ?? ''); @endphp
          <option value="">Tất cả trạng thái</option>
          <option value="Xuất bản" {{ $st==='Xuất bản' ? 'selected' : '' }}>Xuất bản</option>
          <option value="Bản nháp" {{ $st==='Bản nháp' ? 'selected' : '' }}>Bản nháp</option>
        </select>
        <button class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
          <i class="fas fa-filter mr-2"></i> Lọc
        </button>
        <a href="{{ route('admin.faqs.index') }}"
           class="px-5 py-2 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors duration-200">
          Xoá lọc
        </a>
      </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-blue-200 table-container">
      <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
          <thead class="bg-gradient-to-r from-blue-200 to-indigo-200 border-b border-blue-200 shadow-sm">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-20">#</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ảnh bìa</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Câu hỏi</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Chuyên mục</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Thứ tự</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            @forelse($faqs as $f)
              <tr class="hover:bg-blue-100 hover:border-l-4 hover:border-blue-500 transition-colors duration-150 group">
                <td class="px-6 py-4 text-sm text-gray-700">#{{ $f->id }}</td>
                <td class="px-6 py-4">
                    @if($f->cover_image)
                      <img src="{{ asset('storage/'.$f->cover_image) }}" class="rounded" style="width:48px;height:48px;object-fit:cover;">
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900 flex items-start">
                    <i class="fas fa-circle-question text-blue-500 mr-2 mt-0.5"></i>
                    <span class="break-words">{{ $f->question }}</span>
                  </div>
                  @if(!empty($f->answer))
                    <div class="text-xs text-gray-500 mt-1">
                      {{ \Illuminate\Support\Str::limit(strip_tags($f->answer), 120) }}
                    </div>
                  @endif
                </td>

                <td class="px-6 py-4 text-sm text-gray-900">{{ $f->category ?? '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $f->sort_order }}</td>

                <td class="px-6 py-4">
                  <form action="{{ route('admin.faqs.toggle', $f) }}" method="POST">
                    @csrf @method('PATCH')
                    @php $isPub = $f->status === 'Xuất bản'; @endphp
                    <button class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium
                                   {{ $isPub ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                      <span class="w-2 h-2 rounded-full mr-2 {{ $isPub ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                      {{ $f->status }}
                    </button>
                  </form>
                </td>

                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <a href="{{ route('admin.faqs.edit', $f) }}"
                       class="inline-flex items-center px-3 py-2 bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200 hover:scale-105 transition-all duration-200 text-sm font-medium">
                      <i class="fas fa-edit mr-1"></i> Sửa
                    </a>
                    <form action="{{ route('admin.faqs.destroy', $f) }}" method="POST" class="inline"
                          onsubmit="return confirm('Xoá FAQ này?')">
                      @csrf @method('DELETE')
                      <button class="inline-flex items-center px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 hover:scale-105 transition-all duration-200 text-sm font-medium">
                        <i class="fas fa-trash mr-1"></i> Xoá
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Chưa có FAQ.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

     {{-- Pagination footer --}}
<div class="bg-white border-t border-gray-200 px-6 py-4">
  <div class="flex items-center justify-between flex-col sm:flex-row gap-3">
    <div class="text-sm text-gray-700">
      @if($faqs->total() > 0)
        Hiển thị <span class="font-medium">{{ $faqs->firstItem() }}</span>
        đến <span class="font-medium">{{ $faqs->lastItem() }}</span>
        trong tổng số <span class="font-medium">{{ $faqs->total() }}</span> kết quả
      @else
        Không có kết quả
      @endif
    </div>
    

    <div class="w-full sm:w-auto">
      {{-- Tailwind (mặc định của Laravel >=8) --}}
      {{ $faqs->onEachSide(1)->links() }}

      {{-- Nếu layout của bạn dùng Bootstrap 5, HÃY dùng dòng dưới thay cho dòng trên
           và đảm bảo đã load CSS Bootstrap trong layout: --}}
      {{-- {{ $faqs->onEachSide(1)->links('pagination::bootstrap-5') }} --}}
    </div>
  </div>
</div>

    </div>

  </div>
</div>

{{-- Assets giống trang sản phẩm --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
  .container { max-width: none !important; }
  .page-header{
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: 16px; padding: 2rem; margin-bottom: 2rem;
    color: white; position: relative; overflow: hidden;
  }
  .page-title{ font-size: 2.5rem; font-weight: 800; margin-bottom: .5rem; position: relative; z-index: 2; }
  .page-subtitle{ font-size: 1.1rem; opacity: .9; position: relative; z-index: 2; }
  .table-container{ box-shadow: 0 4px 20px rgba(59,130,246,.15); }
  .overflow-x-auto::-webkit-scrollbar{ height:6px; }
  .overflow-x-auto::-webkit-scrollbar-track{ background:white; border-radius:3px; }
  .overflow-x-auto::-webkit-scrollbar-thumb{ background:#3b82f6; border-radius:3px; }
  .overflow-x-auto::-webkit-scrollbar-thumb:hover{ background:#2563eb; }
</style>

<script>
  // Tự submit khi đổi filter (tuỳ chọn)
  document.querySelectorAll('form select[name], form input[name="q"]').forEach(el=>{
    el.addEventListener('change', ()=> el.form && el.form.submit());
  });
</script>
@endsection

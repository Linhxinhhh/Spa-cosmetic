@extends('dashboard.layouts.app')
@section('title','Quản lý Cẩm nang')

@section('content')
<div class="min-h-screen from-slate-50 to-blue-50">
  <div class="container mx-auto px-4 py-8">

    {{-- Flash message --}}
    @if(session('success'))
      <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
      </div>
    @endif

    {{-- Header --}}
    <div class="page-header">
      <h1 class="page-title">
        <i class="fas fa-book-open text-white text-lg"></i>
        Quản lý Cẩm nang
      </h1>
      <p class="page-subtitle">Theo dõi và quản lý bài viết hướng dẫn của Lyn & Spa</p>
      <div class="mt-4 flex flex-wrap gap-3">
        <a href="#" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200">
          <i class="fas fa-download mr-2"></i> Xuất Excel
        </a>
        <a href="{{ route('admin.guides.create') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:from-emerald-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200">
          <i class="fas fa-plus mr-2"></i> Thêm bài viết
        </a>
      </div>
    </div>

    {{-- Stats --}}
    @php
      $collection = $guides instanceof \Illuminate\Pagination\AbstractPaginator ? $guides->getCollection() : collect($guides);
      $totalShowing = $collection->count();
      $publishedCount = $collection->filter(fn($g)=>(int)$g->status===1)->count();
      $draftCount = $collection->filter(fn($g)=>(int)$g->status!==1)->count();
      $viewsSum = $collection->sum('views');
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-list text-blue-600 text-xl"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Bài viết (trang này)</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalShowing }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-emerald-100">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Xuất bản</p>
            <p class="text-2xl font-bold text-gray-900">{{ $publishedCount }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-slate-100">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-file text-slate-600 text-xl"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Nháp</p>
            <p class="text-2xl font-bold text-gray-900">{{ $draftCount }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-orange-100">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-eye text-orange-600 text-xl"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Lượt xem (trang này)</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($viewsSum) }}</p>
          </div>
        </div>
      </div>
    </div>

    {{-- Search & Filter --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <form method="GET" action="{{ route('admin.guides.index') }}"
            class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tiêu đề / tóm tắt..."
               class="px-3 py-2 border border-gray-200 rounded-lg w-72 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
          <i class="fas fa-search mr-1"></i> Tìm kiếm
        </button>
      </form>

      <form method="GET" action="{{ route('admin.guides.index') }}"
            class="flex gap-3 items-center bg-white rounded-xl shadow-lg px-4 py-3 border border-blue-100">
        @php $st = request('status',''); @endphp
        <select name="status"
                class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 w-56">
          <option value=""   {{ $st===''?'selected':'' }}>Tất cả trạng thái</option>
          <option value="1"  {{ $st==='1'?'selected':'' }}>Xuất bản</option>
          <option value="0"  {{ $st==='0'?'selected':'' }}>Nháp</option>
        </select>
        <button type="submit"
                class="px-5 py-2 bg-blue-50 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition-colors duration-200">
          <i class="fas fa-filter mr-2"></i> Lọc
        </button>
        <a href="{{ route('admin.guides.index') }}"
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
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-20">Ảnh</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tiêu đề</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Chuyên mục</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Xuất bản</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lượt xem</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Hành động</th>
          </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
          @forelse($guides as $g)
            @php
              $thumb = $g->thumbnail ?? $g->image ?? $g->cover ?? null;
              $thumbUrl = $thumb ? (Str::startsWith($thumb, ['http://','https://']) ? $thumb : asset('storage/'.$thumb)) : asset('images/default-post.png');
            @endphp
            <tr class="hover:bg-blue-100 hover:border-l-4 hover:border-blue-500 transition-colors duration-150 group">
              <td class="px-6 py-4">
                <img src="{{ $thumbUrl }}" alt="{{ $g->title }}"
                     class="w-12 h-12 rounded-lg object-cover ring-1 ring-gray-200">
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center text-white font-bold text-sm hover:scale-105 transition-transform duration-200">
                    {{ strtoupper(Str::substr($g->title,0,2)) }}
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900 flex items-center">
                      <i class="fas fa-file-alt text-cyan-500 mr-2"></i>
                      {{ $g->title }}
                    </div>
                    <div class="text-xs text-gray-500 break-all">{{ $g->slug }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-900">
                {{ $g->category->name ?? '—' }}
              </td>
              <td class="px-6 py-4">
                @if((int)$g->status === 1)
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-200 text-emerald-900 badge-glow hover:shadow-md hover:bg-emerald-300 transition-all duration-200">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span> Xuất bản
                  </span>
                @else
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-200 text-slate-900 badge-glow hover:shadow-md hover:bg-slate-300 transition-all duration-200">
                    <span class="w-2 h-2 bg-slate-500 rounded-full mr-2"></span> Nháp
                  </span>
                @endif
              </td>
        
            <td class="px-6 py-4 text-sm text-gray-900">
             {{ $g->published_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') ?? '—' }}
  
            </td>
              <td class="px-6 py-4 text-sm text-gray-900">
                <i class="fas fa-eye text-gray-400 mr-2"></i>{{ number_format($g->views) }}
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end space-x-2">
                  <a href="{{ route('admin.guides.edit',$g->guide_id) }}"
                     class="inline-flex items-center px-3 py-2 bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200 hover:scale-105 transition-all duration-200 text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i> Sửa
                  </a>

                 <form action="{{ route('admin.guides.togglePublish', $g->guide_id) }}" method="POST" class="inline">
                  @csrf
                  @method('PATCH')
                  <button class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-800 rounded-lg hover:bg-indigo-200 hover:scale-105 transition-all duration-200 text-sm font-medium">
                    <i class="fas fa-toggle-on mr-1"></i> {{ (int)$g->status ? 'Chuyển thành nháp' : 'Xuất bản' }}
                  </button>
                </form>

                  <form action="{{ route('admin.guides.destroy',$g->guide_id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Xoá bài này?');">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 hover:scale-105 transition-all duration-200 text-sm font-medium">
                      <i class="fas fa-trash mr-1"></i> Xoá
                    </button>
                  </form>

                  <div class="relative inline-block">
                    <button onclick="toggleDropdown({{ $g->guide_id }})"
                            class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 hover:scale-105 transition-all duration-200 text-sm font-medium">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div id="dropdown-{{ $g->guide_id }}"
                         class="hidden absolute right-0 mt-2 w-48 bg-blue-50 rounded-lg shadow-lg border border-blue-200 z-10">
                      <div class="py-2">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-100 hover:text-blue-900">
                          <i class="fas fa-eye mr-2"></i>Xem chi tiết
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-100 hover:text-blue-900">
                          <i class="fas fa-copy mr-2"></i>Nhân bản
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="#" class="block px-4 py-2 text-sm text-red-700 hover:bg-red-100 hover:text-red-900">
                          <i class="fas fa-link mr-2"></i>Sao chép link
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-gray-500">Chưa có bài viết</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination footer --}}
      <div class="bg-white border-t border-gray-200 px-6 py-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
          <div class="text-sm text-gray-700">
            @if($guides instanceof \Illuminate\Pagination\LengthAwarePaginator)
              Hiển thị <span class="font-medium">{{ $guides->firstItem() }}</span>
              đến <span class="font-medium">{{ $guides->lastItem() }}</span>
              trong tổng số <span class="font-medium">{{ $guides->total() }}</span> kết quả
            @else
              Hiển thị <span class="font-medium">{{ $collection->count() }}</span> kết quả
            @endif
          </div>
             <div class="d-flex ms-auto align-items-end gap-2">
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
          <div class="text-sm">
            @if($guides instanceof \Illuminate\Pagination\LengthAwarePaginator)
              {{ $guides->onEachSide(1)->withQueryString()->links('pagination::tailwind') }}
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Dependencies (nếu layout chưa include) --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
  .container { max-width: none !important; }
  .page-header {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
  }
  .page-title { font-size: 2.0rem; font-weight: 800; margin-bottom: 0.25rem; position: relative; z-index: 2; }
  .page-subtitle { font-size: 1.05rem; opacity: 0.9; position: relative; z-index: 2; }
  .badge-glow { box-shadow: 0 0 8px rgba(59,130,246,.3); }
  .table-container { box-shadow: 0 4px 20px rgba(59,130,246,.15); }
  .overflow-x-auto::-webkit-scrollbar { height: 6px; }
  .overflow-x-auto::-webkit-scrollbar-track { background: white; border-radius: 3px; }
  .overflow-x-auto::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 3px; }
  .overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #2563eb; }
  tr:hover { border-left: 4px solid #3b82f6; }
</style>

<script>
  function toggleDropdown(id) {
    const dropdown = document.getElementById(`dropdown-${id}`);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    allDropdowns.forEach(dd => { if (dd.id !== `dropdown-${id}`) dd.classList.add('hidden'); });
    dropdown.classList.toggle('hidden');
  }
  document.addEventListener('click', function (e) {
    if (!e.target.closest('[onclick^="toggleDropdown"]')) {
      document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
  });
  // Tự động submit khi đổi filter
  document.querySelectorAll('select[name], input[type="date"]').forEach(el=>{
    el.addEventListener('change', ()=> el.form && el.form.submit());
  });
  (function () {
      const wrap = document.getElementById('pager-wrap');
      if (!wrap) return;

      const current = parseInt(wrap.dataset.current || '1', 10);
      const last    = parseInt(wrap.dataset.last    || '1', 10);
      const base    = wrap.dataset.base || window.location.pathname;
      const query   = (() => {
        try { return JSON.parse(wrap.dataset.query || '{}'); }
        catch { return {}; }
      })();

      const btnPrev = document.getElementById('btnPrev');
      const btnNext = document.getElementById('btnNext');
      const pageBox = document.getElementById('pageButtons');

      function buildUrl(page) {
        const params = new URLSearchParams(query);
        params.set('page', page);
        return base + '?' + params.toString();
      }

      function addPageBtn(page, active = false) {
        const a = document.createElement('a');
        a.href = buildUrl(page);
        a.textContent = page;
        a.className =
          'px-3 py-2 text-sm font-medium rounded-lg ' +
          (active
            ? 'text-white bg-blue-600 border border-blue-600'
            : 'text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-700');
        pageBox.appendChild(a);
      }

      function addDots() {
        const s = document.createElement('span');
        s.className = 'px-2 text-gray-400 select-none';
        s.textContent = '…';
        pageBox.appendChild(s);
      }

      function renderPages() {
        pageBox.innerHTML = '';

        // Hiển thị: 1 … (current-1) current (current+1) … last
        const range = 1; // đổi 2 nếu muốn rộng hơn
        const start = Math.max(1, current - range);
        const end   = Math.min(last, current + range);

        if (start > 1) {
          addPageBtn(1, current === 1);
          if (start > 2) addDots();
        }

        for (let p = start; p <= end; p++) {
          addPageBtn(p, p === current);
        }

        if (end < last) {
          if (end < last - 1) addDots();
          addPageBtn(last, current === last);
        }
      }

      // Prev/Next
      btnPrev.disabled = current <= 1;
      btnNext.disabled = current >= last;

      btnPrev.addEventListener('click', () => {
        if (current > 1) window.location.href = buildUrl(current - 1);
      });
      btnNext.addEventListener('click', () => {
        if (current < last) window.location.href = buildUrl(current + 1);
      });

      renderPages();
    })();
</script>
@endsection

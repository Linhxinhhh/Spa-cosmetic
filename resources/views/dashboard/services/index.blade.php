@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị dịch vụ')
@section('page-title', 'Dịch vụ')

@push('styles')
<link href="{{asset('admin/giaodien/css/style.css')}}" rel="stylesheet">
@endpush
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp
@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="service-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size:2.5rem;font-weight:700;">
                    <i class="fas fa-concierge-bell mr-3"></i>Quản lý dịch vụ
                </h1>
                
            </div>
    <div class="col-md-4">
      <div class="d-flex justify-content-md-end gap-2">
        {{-- Xuất Excel --}}
        <a href="" class="btn-excel">
          <i class="fas fa-download"></i> Xuất Excel
        </a>
        {{-- Thêm mới --}}
        <a href="{{ route('admin.services.create') }}" class="btn-add">
          <i class="fas fa-plus me-1"></i> Thêm mới
        </a>
      </div>
    </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-modern mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        {{-- Search --}}
        <form action="{{ route('admin.services.index') }}" method="GET"
              class="d-flex gap-2 align-items-center bg-white rounded-xl shadow-lg px-3 py-2 border border-blue-100">
           <input type="text" name="q" value="{{ request('q') }}"
       placeholder="Tìm kiếm dịch vụ theo tên, mô tả..."
       class="px-3 py-2 border border-gray-200 rounded-lg"
       style="min-width:260px; width:300px">

            {{-- giữ lại params lọc khi search --}}
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            <input type="hidden" name="type" value="{{ request('type') }}">
            <input type="hidden" name="featured" value="{{ request('featured') }}">
            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg"><i class="fas fa-search mr-1"></i></button>
        </form>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.services.index') }}" 
              class="d-flex gap-2 align-items-center bg-white rounded-xl shadow-lg px-3 py-2 border border-blue-100">

            <select name="status" class="px-7 py-2 border border-gray-200 rounded-lg">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Tạm ngưng</option>
            </select>

            <select name="category_id" class="px-3 py-2 border border-gray-200 rounded-lg">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>

            <select name="type" class="px-7 py-2 border border-gray-200 rounded-lg">
                <option value="">Loại dịch vụ</option>
                <option value="single" {{ request('type')==='single' ? 'selected' : '' }}>Single</option>
                <option value="combo"  {{ request('type')==='combo'  ? 'selected' : '' }}>Combo</option>
            </select>

            <label class="d-flex align-items-center gap-1 mb-0">
                <input type="checkbox" name="featured" value="1" {{ request('featured') ? 'checked' : '' }}>
                <span>Nổi bật</span>
            </label>

            <button type="submit" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg">
                <i class="fas fa-filter mr-1"></i>Lọc
            </button>
            <a href="{{ route('admin.services.index') }}" class="px-3 py-2 bg-gray-50 text-gray-700 rounded-lg">Xoá lọc</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th style="width:80px">#ID</th>
                    <th class="w-25" style="min-width:260px">Tên dịch vụ</th>
                    <th>Danh mục</th>
                    <th>Loại</th>
                    <th>Giá</th>
                    <th>Thời lượng</th>
                    <th>Nổi bật</th>
                  
                    <th>Hình</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td><strong>#{{ $service->service_id }}</strong></td>
                        <td class="text-left w-25">
                            <strong>{{ $service->service_name }}</strong><br>
                            @if($service->short_desc)
                                <small class="text-muted">{{ Str::limit($service->short_desc, 80) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="color:#1e40af;border-radius:20px;padding:6px 12px">
                                {{ optional($service->category)->category_name ?? 'Không có' }}
                            </span>
                        </td>
                        <td>
                            <span class="chip chip-type">{{ strtoupper($service->type) }}</span>
                        </td>
                        <td>
                            @php
                                $sale = $service->price_sale;
                                $orig = $service->price_original ?? $service->price;
                            @endphp
                            @if(!is_null($sale) && $sale > 0)
                                <span class="price-display">{{ number_format($sale,0,',','.') }}đ</span>
                                <span class="price-old">{{ number_format($orig,0,',','.') }}đ</span>
                            @else
                                <span class="price-display">{{ number_format($orig,0,',','.') }}đ</span>
                            @endif
                        </td>
                        <td><span style="color: #7c3aed" class="">{{ $service->duration }} phút</span></td>
                        <td>
                            @if($service->is_featured)
                                <span class="chip chip-featured">Nổi Bật</span>
                            @else
                                —
                            @endif
                        </td>
                        
                        <td>
                          @php
        // Ưu tiên thumbnail, sau đó images
        $imgPath = $service->thumbnail ?: $service->images;

        // Xây src an toàn cho 3 trường hợp:
        // - Lưu trên storage (services/..)
        // - Đường dẫn cũ trong public/ (uploads/..)
        // - URL tuyệt đối (http/https)
        $src = null;
        if ($imgPath) {
            if (Storage::disk('public')->exists($imgPath)) {
                // Ảnh nằm trong storage/app/public
                $src = Storage::url($imgPath); // => /storage/services/xxx.jpg
            } elseif (Str::startsWith($imgPath, ['http://', 'https://', '//'])) {
                // Trường hợp ảnh là URL tuyệt đối
                $src = $imgPath;
            } else {
                // Ảnh cũ nằm trong public/uploads/... (không qua storage)
                $src = asset($imgPath);
            }
        }
    @endphp

    @if($src)
        <img src="{{ $src }}" class="service-image" alt="{{ $service->service_name }}">
    @else
        <div style="width:80px;height:80px;background:#f3f4f6;border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto">
            <i class="fas fa-image text-muted"></i>
        </div>
    @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.services.edit', $service->service_id) }}" class="btn btn-edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.services.destroy', $service->service_id) }}" method="POST" onsubmit="return confirm('Xoá dịch vụ này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete" title="Xoá"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>Chưa có dịch vụ nào
                        </td>
                    </tr>
                    
                @endforelse
                
            </tbody>
                    {{-- Pagination --}}
            
        </table>


    </div>
      <div class="bg-white border-top px-3 py-3 d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Hiển thị
                <span class="font-weight-bold">{{ $services->firstItem() }}</span> –
                <span class="font-weight-bold">{{ $services->lastItem() }}</span>
                trong tổng số
                <span class="font-weight-bold">{{ $services->total() }}</span> kết quả
            </div>
            <div>
                {{ $services->onEachSide(1)->links() }}
            </div>
        </div>
</div>
<style>
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
<script src="{{asset('admin/giaodien/js/main.js')}}"></script>
@endpush

@endsection
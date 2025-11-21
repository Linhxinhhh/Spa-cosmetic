@extends('dashboard.layouts.app')

@section('page-title','Quản lý phản hồi')

@push('styles')
<style>
  /* Header gradient như trang sản phẩm */
  .page-header{
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: 16px; padding: 2rem; margin-bottom: 2rem;
    color:#fff; position: relative; overflow:hidden;
    box-shadow: 0 10px 25px rgba(30,64,175,.2);
  }

  /* Bảng hiện đại */
  .table-modern{ background:#fff; border-radius: 15px; overflow:hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.08); border:none; }
  .table-modern thead{ background: linear-gradient(135deg,#1e40af 0%,#3b82f6 100%); color:#fff; }
  .table-modern thead th{ border:none; padding:1rem; font-weight:600; text-align:center; }
  .table-modern tbody td{ border:none; padding:1rem; vertical-align:middle; text-align:center;
    border-bottom:1px solid #e5e7eb; }
  .table-modern tbody tr:hover{ background:#f9fbff; }

  /* Badge trạng thái */
  .status-badge{ padding:8px 14px; border-radius:999px; font-size:.8rem; font-weight:700; letter-spacing:.3px; }
  .status-open{ background: linear-gradient(135deg,#f59e0b 0%,#d97706 100%); color:#fff; }
  .status-processing{ background: linear-gradient(135deg,#06b6d4 0%,#0ea5e9 100%); color:#fff; }
  .status-done{ background: linear-gradient(135deg,#10b981 0%,#059669 100%); color:#fff; }

  .search-wrap{ display:flex; gap:.75rem; flex-wrap:wrap; }
  .search-wrap .form-control, .search-wrap .form-select{
    border-radius: 10px; padding:.65rem .9rem; border:1px solid #e2e8f0;
  }
  .btn-primary-custom{
    background: linear-gradient(135deg,#2563eb 0%, #1d4ed8 100%);
    border:none; color:#fff; padding:.7rem 1.1rem; border-radius:10px;
    font-weight:600; box-shadow:0 4px 15px rgba(37,99,235,.3);
    transition: transform .2s ease, box-shadow .2s ease;
  }
  .btn-primary-custom:hover{ transform: translateY(-2px); box-shadow:0 8px 22px rgba(37,99,235,.4); }

  .text-left{ text-align:left !important; }
  .text-truncate-2{
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
  }
  /* Nút icon gọn gàng */
.icon-action{
  display:inline-flex; align-items:center; justify-content:center;
  width:36px; height:36px; border-radius:10px;
  background:#fff; border:1px solid #dbe3f3; color:#1e40af;
  transition:all .2s ease;
}
.icon-action:hover{
  transform:translateY(-1px);
  box-shadow:0 8px 22px rgba(37,99,235,.25);
  border-color:#bcd0ff; color:#1d4ed8;
}

/* Phiên bản gradient (dùng khi muốn nổi bật hơn) */
.icon-action--view{
  background:linear-gradient(135deg,#1e40af 0%,#3b82f6 100%);
  color:#fff; border-color:transparent;
}
.icon-action--view:hover{ filter:brightness(1.05); }

</style>
@endpush

@section('content')
<div class="container-fluid">

  {{-- Header --}}
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="mb-1" style="font-size:2.2rem; font-weight:800;">
          <i class="fas fa-inbox me-2"></i> Quản lý phản hồi
        </h1>
        <p class="mb-0" style="opacity:.9">Xem, lọc và xử lý phản hồi từ khách hàng</p>
      </div>
      <div class="col-md-4 d-flex justify-content-md-end mt-3 mt-md-0">
        {{-- (tuỳ chọn) Nút xuất file / báo cáo — để trống route nếu chưa có --}}
        {{-- <a href="{{ route('admin.contacts.export') }}" class="btn btn-primary-custom"><i class="fas fa-download me-2"></i>Xuất Excel</a> --}}
      </div>
    </div>
  </div>

  {{-- Alert --}}
  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-3">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
  @endif

  {{-- Bộ lọc --}}
  <div class="bg-white rounded-3 shadow-sm p-8 mb-3 border border-blue-50">
    <form method="get" class="search-wrap">
      <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="Tìm tên / SĐT / email / nội dung…" style="min-width:260px">
      <select class="form-select" name="status" style="min-width:180px">
        <option value="">Tất cả trạng thái </option>
        <option value="open"       @selected($status==='open')>Mới</option>
        <option value="processing" @selected($status==='processing')>Đang xử lý</option>
        <option value="done"       @selected($status==='done')>Hoàn tất</option>
      </select>
      <button class="btn btn-primary-custom"><i class="fas fa-filter me-2"></i>Lọc</button>
      @if(request()->hasAny(['q','status']) && (request('q') || request('status')))
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-light border ms-auto">
          <i class="fas fa-eraser me-1"></i> Xoá lọc
        </a>
      @endif
    </form>
  </div>

  {{-- Bảng --}}
  <div class="table-responsive">
    <table class="table table-modern">
      <thead>
        <tr>
          <th style="width:70px">ID</th>
          <th style="width:200px">Khách</th>
          <th style="width:260px">Liên lạc</th>
          <th>Chủ đề / Nội dung</th>
          <th style="width:160px">Trạng thái</th>
          <th style="width:190px">Thời gian</th>
          <th style="width:120px">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @forelse($contacts as $c)
          @php
            $cls = match($c->status){
              'open' => 'status-open',
              'processing' => 'status-processing',
              'done' => 'status-done',
              default => 'status-open'
            };
          @endphp
          <tr>
            <td><strong>#{{ $c->contact_id }}</strong></td>

            <td class="text-left">
              <div class="fw-semibold">{{ $c->name }}</div>
              @if($c->customer_id)
                <div class="small text-muted">ID KH: {{ $c->customer_id }}</div>
              @endif
            </td>

            <td class="text-left small">
              <div><i class="fas fa-phone me-1 text-muted"></i>{{ $c->phone }}</div>
              @if($c->email)
                <div class="mt-1"><i class="fas fa-at me-1 text-muted"></i>{{ $c->email }}</div>
              @endif
            </td>

            <td class="text-left">
              @if($c->subject)
                <div class="fw-semibold mb-1"><i class="fas fa-bookmark me-1 text-muted"></i>{{ $c->subject }}</div>
              @endif
              <div class="text-muted text-truncate-2">{{ \Illuminate\Support\Str::limit($c->message, 140) }}</div>
            </td>

            <td>
              <span class="status-badge {{ $cls }}">
                {{ $c->status === 'open' ? 'Mới' : ($c->status === 'processing' ? 'Đang xử lý' : 'Hoàn tất') }}
              </span>
            </td>

            <td class="small">
              <div>{{ $c->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i')  }}</div>
              @if($c->responded_at)
                <div class="mt-1 text-muted">
                  <i class="far fa-paper-plane me-1"></i>Phản hồi: {{ $c->responded_at->format('d/m/Y H:i') }}
                </div>
              @endif
            </td>

<td class="text-center">
  <a href="{{ route('admin.contacts.show',$c) }}"
     class="icon-action"
     title="Xem chi tiết" aria-label="Xem chi tiết">
    <i class="fas fa-eye"></i>
  </a>
</td>


          </tr>
        @empty
          <tr>
            <td colspan="7" class="py-5">
              <div class="text-muted">
                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                Chưa có liên hệ nào.
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
      
    </table>
    @php
  /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $p */
  $p = $categories ?? $contacts; // đổi tuỳ biến bạn đang dùng
  $first = $p->firstItem() ?? 0;
  $last  = $p->lastItem()  ?? 0;
  $total = $p->total()     ?? 0;
@endphp

<div class="bg-white border-t border-gray-200 px-6 py-4">
  <div class="flex items-center justify-between">
    <div class="text-sm text-gray-700">
      Hiển thị <span class="font-medium">{{ $first }}</span> đến
      <span class="font-medium">{{ $last }}</span>
      trong tổng số <span class="font-medium">{{ $total }}</span> kết quả
    </div>

    <div class="flex items-center space-x-2">
      {{-- Prev --}}
      @php $prevDisabled = $p->onFirstPage(); @endphp
      <a href="{{ $prevDisabled ? '#' : $p->previousPageUrl() }}"
         class="px-3 py-2 text-sm font-medium {{ $prevDisabled ? 'text-gray-400 border-gray-200 cursor-not-allowed' : 'text-gray-600 hover:text-gray-700 hover:bg-gray-50 border-gray-300' }} bg-white border rounded-lg"
         @if($prevDisabled) aria-disabled="true" @endif>
        <i class="fas fa-chevron-left mr-1"></i> Trước
      </a>

      {{-- Page numbers (hiển thị trang hiện tại ±1; chỉnh tuỳ ý) --}}
      @php
        $start = max(1, $p->currentPage()-1);
        $end   = min($p->lastPage(), $p->currentPage()+1);
      @endphp
      @for($page = $start; $page <= $end; $page++)
        @if($page == $p->currentPage())
          <span class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
            {{ $page }}
          </span>
        @else
          <a href="{{ $p->url($page) }}"
             class="px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700">
            {{ $page }}
          </a>
        @endif
      @endfor

      {{-- Next --}}
      @php $nextDisabled = !$p->hasMorePages(); @endphp
      <a href="{{ $nextDisabled ? '#' : $p->nextPageUrl() }}"
         class="px-3 py-2 text-sm font-medium {{ $nextDisabled ? 'text-gray-400 border-gray-200 cursor-not-allowed' : 'text-gray-600 hover:text-gray-700 hover:bg-gray-50 border-gray-300' }} bg-white border rounded-lg"
         @if($nextDisabled) aria-disabled="true" @endif>
        Sau <i class="fas fa-chevron-right ml-1"></i>
      </a>
    </div>
  </div>
</div>

    
  </div>


</div>
@endsection

@push('scripts')
<script>
  // Bổ sung Font Awesome nếu layout chưa có
  if (!document.querySelector('link[href*="font-awesome"]')) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css';
    document.head.appendChild(link);
  }
</script>
@endpush

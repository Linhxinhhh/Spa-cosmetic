@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị nhân viên')
@section('page-title', 'Nhân viên')

@push('styles')
    <link href="{{ asset('admin/giaodien/css/style.css') }}" rel="stylesheet">
   <style>
    /* Bảng rộng 100% và các cột phân bổ đều */
.table-modern {
    width: 100%;
    table-layout: fixed;       /* ⚡ Giúp các cột chia đều */
    border-collapse: collapse;
}

.table-modern th,
.table-modern td {
    padding: 14px 12px;
    vertical-align: middle;
    text-align: left;
    white-space: nowrap;       /* Không xuống dòng lung tung */
    overflow: hidden;
    text-overflow: ellipsis;   /* Hiển thị "..." nếu quá dài */
}

/* Tăng độ rộng tự nhiên theo nội dung của một số cột */
.table-modern th:nth-child(1),
.table-modern td:nth-child(1) {
    width: 7%;     /* Cột ID */
}

.table-modern th:nth-child(2),
.table-modern td:nth-child(2) {
    width: 20%;    /* Tên nhân viên */
}

.table-modern th:nth-child(3),
.table-modern td:nth-child(3) {
    width: 22%;    /* Email */
}

.table-modern th:nth-child(4),
.table-modern td:nth-child(4) {
    width: 14%;    /* Phone */
}

.table-modern th:nth-child(5),
.table-modern td:nth-child(5) {
    width: 22%;    /* Address */
}

.table-modern th:nth-child(6),
.table-modern td:nth-child(6) {
    width: 15%;    /* Action buttons */
}

/* Nút hành động cân chỉnh */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.btn-action {
    padding: 6px 10px;
    border-radius: 6px;
}

   </style>
@endpush

@section('content')
    <div class="staff-container">

        {{-- Header --}}
        <div class="service-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1>
                        <i class="fas fa-users"></i>Quản lý nhân viên
                    </h1>
                </div>
                <div class="col-md-4 text-right">
                    <a href="{{ route('admin.staffs.create') }}" class="btn-add">
                        <i class="fas fa-plus"></i>
                        <span>Thêm mới</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if(session('success'))
            <div class="alert alert-modern mb-4">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Tên nhân viên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($staffs as $staff)
                            <tr>
                                <td>
                                    <span class="staff-id">#{{ $staff->user_id }}</span>
                                </td>
                                <td>
                                    <span class="staff-name">{{ $staff->name }}</span>
                                </td>
                                <td>
                                    <span class="staff-email">{{ $staff->email }}</span>
                                </td>
                                <td>
                                    <span class="staff-phone">{{ $staff->phone }}</span>
                                </td>
                                <td>
                                    <span class="staff-address" title="{{ $staff->address ?? 'Chưa cập nhật' }}">
                                        {{ $staff->address ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.staffs.edit', $staff) }}" 
                                           class="btn-action btn-edit"
                                           title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.staffs.destroy', $staff) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn-action btn-delete"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <p>Chưa có nhân viên nào trong hệ thống</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($staffs->hasPages())
            <div class="mt-4">
                {{ $staffs->links() }}
            </div>
        @endif

    </div>
@endsection
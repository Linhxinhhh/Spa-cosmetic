@extends('dashboard.layouts.app')
@section('content')
<div class="container">
    <h1>Chi tiết Nhân viên: {{ $staff->name }}</h1>
    <div class="row">
        <div class="col-md-4">
            <img src="{{ $staff->avatar_url }}" alt="Avatar" width="150" class="rounded-circle mb-3">
        </div>
        <div class="col-md-8">
            <p><strong>ID:</strong> {{ $staff->staff_id }}</p>
            <p><strong>User ID:</strong> {{ $staff->user_id ?? 'N/A' }} ({{ $staff->user?->name ?? 'Chưa liên kết' }})</p>
            <p><strong>Tên:</strong> {{ $staff->name }}</p>
            <p><strong>Email:</strong> {{ $staff->email }}</p>
            <p><strong>SĐT:</strong> {{ $staff->phone }}</p>
            <p><strong>Chức vụ:</strong> {{ $staff->position }}</p>
           
            <p><strong>Trạng thái:</strong> {{ $staff->status ? 'Hoạt động' : 'Không hoạt động' }}</p>
            <p><strong>Số cuộc hẹn:</strong> {{ $staff->appointments->count() }}</p>
        </div>
    </div>
    <a href="{{ route('admin.staffs.edit', $staff) }}" class="btn btn-warning">Sửa</a>
    <a href="{{ route('admin.staffs.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
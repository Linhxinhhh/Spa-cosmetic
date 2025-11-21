@extends('dashboard.layouts.app')

@section('page-title', 'Thêm khách hàng')

@section('content')
<div class="container">
    <h2>Thêm khách hàng</h2>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary mb-3">← Quay lại</a>

    <form action="{{ route('admin.customers.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tên</label>
            <input type="text" name="name" class="form-control" required>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <input type="text" name="address" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày sinh</label>
            <input type="date" name="birthday" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Điểm tích lũy</label>
            <input type="number" name="loyalty_points" class="form-control" value="0">
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
    </form>
</div>
@endsection

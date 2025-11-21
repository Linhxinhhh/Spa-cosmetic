@extends('dashboard.layouts.app')

@section('page-title', 'Thêm Banner')

@section('content')
<div class="container">
    <h2>Thêm Banner</h2>
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Hình ảnh</label>
            <input type="file" name="image" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Link</label>
            <input type="text" name="link" class="form-control">
        </div>
      <div class="mb-3">
            <label>Vị trí</label>
            <select name="position" class="form-control">
                <option value="homepage_top">Trang chủ - Trên</option>
                <option value="homepage_bottom">Trang chủ - Dưới</option>
                <option value="sidebar">Thanh bên</option>
                <option value="popup">Popup</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Trạng thái</label>
            <select name="status" class="form-control">
                <option value="1">Hoạt động</option>
                <option value="0">Ngưng hoạt động</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Ngày bắt đầu</label>
            <input type="date" name="start_date" class="form-control">
        </div>
        <div class="mb-3">
            <label>Ngày kết thúc</label>
            <input type="date" name="end_date" class="form-control">
        </div>
        <button class="btn btn-success">Lưu</button>
    </form>
</div>
@endsection

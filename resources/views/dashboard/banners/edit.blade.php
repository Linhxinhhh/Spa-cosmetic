@extends('dashboard.layouts.app')

@section('page-title', 'Sửa Banner')

@section('content')
<div class="container">
    <h2>Sửa Banner</h2>
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ $banner->title }}" required>
        </div>
        <div class="mb-3">
            <label>Hình ảnh</label><br>
            <img src="{{ asset('storage/'.$banner->image) }}" width="120" class="mb-2">
            <input type="file" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label>Link</label>
            <input type="text" name="link" class="form-control" value="{{ $banner->link }}">
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
                <option value="1" {{ $banner->status ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ !$banner->status ? 'selected' : '' }}>Ngưng hoạt động</option>
            </select>
        </div>
      <div class="mb-3">
    <label>Ngày bắt đầu</label>
    <input 
        type="date" 
        name="start_date" 
        class="form-control" 
        value="{{ old('start_date', $banner->start_date) }}">
</div>

<div class="mb-3">
    <label>Ngày kết thúc</label>
    <input 
        type="date" 
        name="end_date" 
        class="form-control" 
        value="{{ old('end_date', $banner->end_date) }}">
</div>

        <button class="btn btn-success">Cập nhật</button>
    </form>
</div>
@endsection

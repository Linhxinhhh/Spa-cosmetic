@extends('dashboard.layouts.app')
@section('title','Thêm bài Cẩm nang')
@section('content')
<div class="container-fluid py-3">
  <h3 class="mb-3">Thêm bài viết</h3>
{{-- Header --}}
  <div class="create-header">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="mb-2" style="font-size: 2.2rem; font-weight: 700;">
          <i class="fas fa-pen-nib mr-3"></i>{{ isset($guide) ? 'Cập nhật bài viết' : 'Thêm bài viết mới' }}
        </h1>
        <p class="mb-0" style="font-size: 1.05rem; opacity: .9;">
          Nhập nội dung và thông tin SEO cho bài viết Cẩm nang
        </p>
      </div>
      <div class="col-md-4 text-right">
        <a href="{{ route('admin.guides.index') }}" class="btn btn-cancel">
          <i class="fas fa-arrow-left mr-2"></i>Quay lại
        </a>
      </div>
    </div>
  </div>
  <form action="{{ route('admin.guides.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('dashboard.guides_admin.form', ['guide' => null])
  </form>
</div>
@endsection

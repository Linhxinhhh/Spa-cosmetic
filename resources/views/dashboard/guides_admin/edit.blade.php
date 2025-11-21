@extends('dashboard.layouts.app')
@section('title','Sửa: '.$guide->title)
@section('content')
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Sửa bài: {{ $guide->title }}</h3>
    <a href="{{ route('admin.guides.index') }}" class="btn btn-light">← Quay lại danh sách</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('admin.guides.update', ['guide' => $guide->getKey()]) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('dashboard.guides_admin.form', ['guide' => $guide,
      'categories'  => $categories,
      'tags'        => $tags])
  </form>
</div>
@endsection

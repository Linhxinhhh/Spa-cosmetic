@extends('dashboard.layouts.app')
@section('page-title', 'Chỉnh sửa Hỏi đáp #'.$faq->id)

@section('content')
<div class="container-xl py-4">
  {{-- Header --}}
  <div class="page-header" style="background:linear-gradient(135deg,#1e40af 0%,#3b82f6 100%);border-radius:16px;padding:20px;color:#fff;">
    <h1 class="page-title m-0" style="font-weight:800;">
      <i class="fas fa-pen-to-square me-2"></i> Chỉnh sửa Hỏi đáp
    </h1>
    <p class="page-subtitle mb-0">#{{ $faq->id }} — Cập nhật nội dung câu hỏi & trả lời</p>
  </div>

  {{-- Alerts --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $e)
        <div>{{ $e }}</div>
      @endforeach
    </div>
  @endif
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('dashboard.faq.form', [
          'faq' => $faq,
          'submitLabel' => 'Cập nhật'
        ])
      </form>
    </div>
  </div>

  <div class="mt-3 d-flex gap-2">
    <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
    </a>
  </div>
</div>
@endsection

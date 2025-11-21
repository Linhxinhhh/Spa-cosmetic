@extends('Users.layouts.home')
@section('title', 'Kết quả thanh toán')
@section('content')
<div class="container py-4">
  @if($success ?? false)
    <div class="alert alert-success">{{ $msg ?? 'Thanh toán thành công' }}</div>
  @else
    <div class="alert alert-danger">{{ $msg ?? 'Thanh toán thất bại' }}</div>
  @endif

  <a href="{{ route('users.orders.index') }}" class="btn btn-outline-secondary">Về đơn hàng</a>
</div>
@endsection

@extends('Users.layouts.home')

@section('title','Giỏ hàng')

@push('styles')

@endpush

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg border-0 rounded-4 text-center p-4">

                <div class="mt-3">
                    <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: #28a745;"></i>
                </div>

                <h2 class="mt-3 fw-bold" style="color: #28a745;">
                    Thanh toán thành công!
                </h2>

                <p class="text-muted mt-2">
                    Cảm ơn bạn đã thanh toán. Đơn hàng của bạn đã được ghi nhận và đang được xử lý.
                </p>

                <div class="mt-4">
                    <a href="{{ route('users.home') }}" class="btn btn-success px-4 py-2 rounded-pill">
                        Quay về trang chủ
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

  
@endsection

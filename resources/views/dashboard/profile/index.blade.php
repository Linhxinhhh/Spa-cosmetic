@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Tài khoản')
@section('breadcrumb-child', 'Thông tin cá nhân')
@section('page-title', 'Thông tin cá nhân')

@section('content')
@php
    // Lấy user đúng guard ở khu admin (fallback sang web nếu cần)
    $u = auth('admin')->user() ?: auth()->user();

    // Ảnh mặc định
     $fallbackAvatar = url('/images/profile/user.png');

    // Nếu DB lưu URL Cloudinary thì dùng thẳng; nếu rỗng dùng fallback
    $avatarUrl = $u?->avatar ? trim($u->avatar) : $fallbackAvatar;

    // Roles (nếu có quan hệ many-to-many)
    $roles = $u?->roles ?? collect();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="product-header">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div>
                    <h1 class="mb-2" style="font-size: 2.5rem; font-weight: 700;">
                        <i class="fas fa-user mr-3"></i>Thông tin cá nhân
                    </h1>
                    <p class="text-white opacity-90">Xem thông tin chi tiết tài khoản của bạn</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary-custom">
                        <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="bg-white rounded-xl shadow-lg border border-blue-100">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Avatar Section -->
                    @php
                    $u = auth('admin')->user() ?: auth()->user();
                    @endphp
                    <div class="text-center">
                        <img src="{{ $u?->avatar_url }}"
                             class="product-image mx-auto mb-4"
                             alt="User Image">

                        <h3 class="text-xl font-semibold text-gray-900">{{ $u?->name ?? '—' }}</h3>
                        <p class="text-gray-500">{{ $u?->email ?? '—' }}</p>
                    </div>

                    <!-- Information Cards -->
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <h4 class="font-medium text-gray-500 mb-2 text-sm">Thông tin cơ bản</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium text-gray-700">Họ tên:</span> {{ $u?->name ?? '—' }}</p>
                                <p><span class="font-medium text-gray-700">Email:</span> {{ $u?->email ?? '—' }}</p>
                                <p><span class="font-medium text-gray-700">Số điện thoại:</span> {{ $u?->phone ?: 'Chưa cập nhật' }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <h4 class="font-medium text-gray-500 mb-2 text-sm">Thông tin khác</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium text-gray-700">Địa chỉ:</span> {{ $u?->address ?: 'Chưa cập nhật' }}</p>
                                <p><span class="font-medium text-gray-700">Ngày tạo:</span> {{ $u?->created_at?->format('d/m/Y') ?? '—' }}</p>
                                <p><span class="font-medium text-gray-700">Vai trò:</span>
                                    @forelse($roles as $role)
                                        <span class="status-badge status-active">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-gray-500">Chưa gán</span>
                                    @endforelse
                                </p>
                            </div>
                        </div>
                    </div>
                </div> <!-- grid -->
            </div> <!-- p-6 -->
        </div> <!-- card -->
    </div>
</div>

<!-- Dependencies -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
    .container { max-width: none !important; }
    .product-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white; padding: 2rem; border-radius: 15px; margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(30, 64, 175, 0.2);
    }
    .btn-primary-custom {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none; color: white; padding: 12px 24px; border-radius: 10px;
        font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(37,99,235,.3);
    }
    .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(37,99,235,.4); }
    .status-badge { padding: 8px 16px; border-radius: 25px; font-size: .875rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
    .status-active { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
    .product-image { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,.1); transition: transform .3s ease; border: 4px solid white; }
    .product-image:hover { transform: scale(1.1); }
</style>
@endsection

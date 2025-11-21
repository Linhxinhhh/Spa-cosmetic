@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Chỉnh sửa thông tin')
@section('page-title', 'Chỉnh sửa thông tin')

@section('content')
@php
  
    $u = auth('admin')->user() ?: auth()->user();

    // Ảnh mặc định (nếu DB không có avatar)
     $fallbackAvatar = url('/images/profile/user.png');

    // Lấy URL ảnh: nếu cột avatar rỗng thì dùng fallback
    $avatarUrl = $u?->avatar ? trim($u->avatar) : $fallbackAvatar;
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="product-header">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div>
                    <h1 class="mb-2" style="font-size: 2.5rem; font-weight: 700;">
                        <i class="fas fa-user-edit mr-3"></i>Chỉnh sửa thông tin
                    </h1>
                    <p class="text-white opacity-90">Cập nhật thông tin cá nhân của bạn</p>
                </div>
            </div>
        </div>

        <!-- Profile Edit Form -->
        <div class="bg-white rounded-xl shadow-lg border border-blue-100">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Avatar Section -->
                        @php
                        // Nếu có updated_at thì lấy timestamp của user để bust cache, không thì dùng time()
                        $cacheBuster = $u?->updated_at?->timestamp ?? time();
                    @endphp
                        <div class="text-center">
                            <img id="avatar-preview"
                            src="{{ $u?->avatar_url }}?t={{ $cacheBuster }}"
                            class="product-image mx-auto mb-4"
                            alt="User Image">

                            <div class="form-group">
                                <label for="avatar" class="text-sm font-medium text-gray-600">Ảnh đại diện</label>
                                <input type="file" class="form-control-modern mt-2 w-full" id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                    <span class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="md:col-span-2 space-y-6">
                            <div class="form-group">
                                <label for="name" class="text-sm font-medium text-gray-600">Họ tên</label>
                                <input type="text"
                                       class="form-control-modern w-full @error('name') ring-2 ring-red-500 @enderror"
                                       id="name" name="name"
                                       value="{{ old('name', $u?->name) }}" required>
                                @error('name')
                                    <span class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="text-sm font-medium text-gray-600">Email</label>
                                <input type="email"
                                       class="form-control-modern w-full bg-gray-100 cursor-not-allowed"
                                       id="email" value="{{ $u?->email }}" disabled>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="text-sm font-medium text-gray-600">Số điện thoại</label>
                                <input type="text"
                                       class="form-control-modern w-full @error('phone') ring-2 ring-red-500 @enderror"
                                       id="phone" name="phone"
                                       value="{{ old('phone', $u?->phone) }}">
                                @error('phone')
                                    <span class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="address" class="text-sm font-medium text-gray-600">Địa chỉ</label>
                                <textarea class="form-control-modern w-full @error('address') ring-2 ring-red-500 @enderror"
                                          id="address" name="address" rows="4">{{ old('address', $u?->address) }}</textarea>
                                @error('address')
                                    <span class="text-sm text-red-600 mt-1" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-save mr-2"></i>Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.profile.index') }}" class="btn btn-edit">
                            <i class="fas fa-times mr-2"></i>Hủy bỏ
                        </a>
                    </div>
                </div>
            </form>
        </div>
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
        font-weight: 600; transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    }
    .btn-edit {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border: none; color: white; padding: 8px 16px; border-radius: 8px;
        font-size: 0.875rem; transition: all 0.3s ease;
    }
    .btn-edit:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(14,165,233,.4); }
    .form-control-modern {
        border-radius: 8px; border: 1px solid #d1d5db; padding: 12px 16px; font-size: 14px; transition: all .2s;
    }
    .form-control-modern:focus {
        border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.1); outline: none;
    }
    .product-image {
        width: 150px; height: 150px; object-fit: cover; border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,.1); transition: transform .3s ease;
    }
    .product-image:hover { transform: scale(1.1); }
</style>

<script>
document.getElementById('avatar')?.addEventListener('change', function (event) {
    const output = document.getElementById('avatar-preview');
    if (event.target.files && event.target.files[0]) {
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function () { URL.revokeObjectURL(output.src); }
    }
});
</script>
@endsection

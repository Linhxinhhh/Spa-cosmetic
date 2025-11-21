<aside id="application-sidebar-brand"
    class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full transform hidden xl:block xl:translate-x-0 xl:end-auto xl:bottom-0 fixed    left-0 with-vertical  z-[999] shrink-0 w-[270px] shadow-md xl:rounded-md rounded-none bg-white left-sidebar transition-all duration-300">
    <div class="p-4">
        <a href="{{ route('admin.dashboard') }}" class="text-nowrap">
            <img  src="{{ asset('dashboard/images/logos/logoreplace.png') }}" style="width:150px;height:137px;border-radius:20%;margin-top:10px;margin-left:40px; align-items: center;" alt="Logo-Dark" />
        </a>
    </div>
    <div class="scroll-sidebar" data-simplebar="">
        <nav class="w-full flex flex-col sidebar-nav px-4 mt-5">
            <ul id="sidebarnav" class="text-gray-600 text-sm">
                <!-- Menu items -->
                <li class="text-xs font-bold pb-[5px]">
                    <i class="ti ti-dots nav-small-cap-icon text-lg hidden text-center"></i>
                    <span class="text-xs text-gray-400 font-semibold">TRANG CHỦ</span>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-house"></i> <span>Quản trị</span>
                    </a>
                </li>
                <li class="text-xs font-bold mb-4 mt-6">
                    <i class="ti ti-dots nav-small-cap-icon text-lg hidden text-center"></i>
                    <span class="text-xs text-gray-400 font-semibold">Chức năng</span>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.product_categories.index')}}">
                        <i class="bi bi-box2-heart"></i> <span>Danh mục sản phẩm</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.service_categories.index')}}">
                        <i class="bi bi-emoji-heart-eyes"></i> <span>Danh mục dịch vụ </span>
                    </a>
                </li>

                
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.products.index')}}">
                        <i class="bi bi-box-seam"> </i><span>Sản Phẩm</span>
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.services.index')}}">
                        <i class="bi bi-person-hearts"></i> <span>Dịch Vụ</span>
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.brands.index')}}">
                        <i class="bi bi-tag me-2"></i><span>Thương Hiệu</span>
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.customers.index')}}">
                       <i class="bi bi-people me-2"></i><span>Khách Hàng</span>
                    </a>
                </li>
                 <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.appointments.index')}}">
                       <i class="bi bi-people me-2"></i><span>Lịch Hẹn</span>
                    </a>
                </li>
                 <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.treatment-plans.index')}}">
                       <i class="bi bi-people me-2"></i><span>Liệu Trình</span>
                    </a>
                </li>               
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.orders.index')}}">
                        <i class="bi bi-receipt me-2"></i> <span>Đơn Hàng</span>
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="#">
                        <i class="bi bi-ticket-perforated me-2"></i> <span>Khuyến Mãi</span>
                    </a>
                </li>
                   <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.banners.index')}}">
                        <i class="fas fa-sliders-h"></i>  <span>Banner</span>
                    </a>
                </li>
                <li class="sidebar-item">
                <a
                    href="{{ route('admin.guides.index') }}"
                    class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-ful
                        {{ request()->routeIs('admin.guides.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                    <i class="fas fa-newspaper"></i>
                    <span>Bài viết</span>
                </a>
                </li>
                  <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.contacts.index')}}">
                        <i class="fas fa-inbox"></i>  <span>Phản hồi</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.faqs.index')}}">
                        <i class="bi bi-question-circle-fill text-primary me-2" aria-hidden="true"></i> <span>Hỏi đáp</span>
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="#">
                        <i class="bi bi-star-fill text-warning me-2"></i> <span>Đánh giá</span>
                    </a>
                </li>

                <li class="text-xs font-bold mb-4 mt-8">
                    <i class="ti ti-dots nav-small-cap-icon text-lg hidden text-center"></i>
                    <span class="text-xs text-gray-400 font-semibold">Thống Kê</span>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="#">
                        <i class="bi bi-bar-chart me-2 text-success"></i> <span>Biểu Đồ</span>
                    </a>
                </li>

                <li class="text-xs font-bold mb-4 mt-8">
                    <i class="ti ti-dots nav-small-cap-icon text-lg hidden text-center"></i>
                    <span class="text-xs text-gray-400 font-semibold">AUTH</span>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full"
                        href="{{route('admin.user_roles.index')}}">
                        <i class="bi bi-shield-lock me-2 text-primary"></i><span>Phân Quyền</span>
                    </a>
                </li>
                
    @auth('admin')
<li class="sidebar-item">
    <form action="{{ route('admin.logout') }}" method="POST" class="w-full">
        @csrf
        <button type="submit"
                class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full text-start">
             <i class="bi bi-box-arrow-in-right me-2 text-primary"></i>
            <span>Đăng nhập</span>
        </button>
    </form>
</li>
<li class="sidebar-item">
    <form action="{{ route('admin.login') }}" method="POST" class="w-full">
        @csrf
        <button type="submit"
                class="sidebar-link gap-3 py-2.5 my-1 text-base flex items-center relative rounded-md text-gray-500 w-full text-start">
            <i class="ti ti-login ps-2 text-2xl"></i>
            <span>Đăng xuất</span>
        </button>
    </form>
</li>
@endauth

            </ul>
              
                
                <!-- Các menu item khác -->
            </ul>
        </nav>
    </div>
</aside>

<style>
    .sidebar-item ul {
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
.application-sidebar-brand{
  position: fixed;
  top: 70px;                 /* sát ngay dưới header */
  left: 0;
  width: 270px;
  height: calc(100vh - 90px);/* phần còn lại của màn hình */
  background: #fff;
  box-shadow: 4px 0 15px rgba(0,0,0,0.1);
  z-index: 1100;
  overflow-y: auto;
  transition: all .3s ease;
}



.application-sidebar-brand .scroll-sidebar {
    height: 100%;
}

.application-sidebar-brand .sidebar-nav {
    padding: 0 1rem;
}

.application-sidebar-brand .sidebar-item {
    margin-bottom: 0.5rem;
}

.application-sidebar-brand .sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.625rem 1rem;
    color: #4b5563;
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-family: 'Inter', sans-serif;
    transition: all 0.2s ease;
}

.application-sidebar-brand .sidebar-link:hover,
.application-sidebar-brand .sidebar-link.active {
    background: #f1f5f9;
    color: #1e293b;
}

.application-sidebar-brand .sidebar-link i {
    font-size: 1.125rem;
}

.application-sidebar-brand .nav-small-cap-icon {
    display: none;
}

.application-sidebar-brand .text-gray-400 {
    color: #9ca3af;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Responsive Design */
@media (max-width: 1279px) { /* Dưới xl (1280px) */
    .application-sidebar-brand {
        top: 60px; /* Giả định header chính thấp hơn trên mobile */
        width: 240px;
        height: calc(100vh - 60px);
        transform: translateX(-100%);
    }

    .application-sidebar-brand.hs-overlay-open {
        transform: translateX(0);
    }

    .fixed-header-secondary {
        left: 240px; /* Điều chỉnh theo chiều rộng sidebar trên mobile */
    }
}

@media (min-width: 1280px) {
    .application-sidebar-brand {
        top: 90px; /* Giữ nguyên cho màn hình lớn */
        left: 0;
        display: block;
    }

    .fixed-header-secondary {
        left: 270px; /* Điều chỉnh theo chiều rộng sidebar */
    }
}

/* Đảm bảo nội dung không bị che */
.content-wrapper {
    margin-left: 270px;
    padding-top: 170px; /* Tổng chiều cao header chính (90px) + header phụ (80px) */
}

@media (max-width: 1279px) {
    .content-wrapper {
        margin-left: 0;
        padding-top: 120px; /* Chỉ header chính + header phụ trên mobile */
    }
}
</style>

<style>
    .fixed-header-secondary {
        position: fixed;
        top: 60px;;
    /* Đặt sát với header chính, điều chỉnh nếu header chính thay đổi chiều cao */
    left: 0;
    right: 0;
    z-index: 1000;
    background: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 0 0 8px 8px;
    padding: 1rem 1.5rem;
    margin: 0;
    margin-left: 290px;
       
    }

    .fixed-header-secondary nav {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .content-header .breadcrumb {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .content-header .breadcrumb i {
        font-size: 1rem;
        margin-right: 0.5rem;
    }

    .content-header .breadcrumb .parent {
        display: flex;
        align-items: center;
        color: #6b7280;
    }

    .content-header .breadcrumb .separator {
        margin: 0 0.5rem;
        color: #d1d5db;
    }

    .content-header .breadcrumb .child {
        color: #374151;
        font-weight: 500;
    }

    .content-header .page-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin-top: 0.25rem;
    }

    .hs-dropdown-toggle {
        cursor: pointer;
    }

    .hs-dropdown-menu {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        min-width: 200px;
        margin-top: 0.5rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .hs-dropdown-menu.show {
        opacity: 1;
    }

    .card-body a {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
    }

    .card-body a:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .fixed-header-secondary {
            top: 50px; /* Điều chỉnh nếu header chính thay đổi chiều cao */
            padding: 0.75rem 1rem;
        }

        .content-header .page-title {
            font-size: 1rem;
        }

        .content-header .breadcrumb {
            font-size: 0.75rem;
        }

        .hs-dropdown-menu {
            min-width: 160px;
        }
    }
</style>

<header class="fixed-header-secondary">
    <nav class="w-full flex items-center justify-between" aria-label="Global">
        <div class="content-header">
            <div class="flex items-center justify-between px-0 py-3">
                <!-- Breadcrumb -->
                <div>
                    <div class="breadcrumb">
                        <span class="parent">
                            <i class="ti ti-home mr-1.5"></i>
                            @yield('breadcrumb-parent', 'Pages')
                        </span>
                        <i class="ti ti-chevron-right separator"></i>
                        <span class="child">@yield('breadcrumb-child', 'Dashboard')</span>
                    </div>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                </div>
            </div>
        </div>  
        
        <div class="flex items-center gap-4">
            <div class="hs-dropdown relative inline-flex [--placement:bottom-right] sm:[--trigger:hover]">
                <a class="hs-dropdown-toggle cursor-pointer align-middle rounded-full">
                    <img src="{{ auth('admin')->user()?->avatar_url }}" class="w-9 h-9 rounded-full" alt="Avatar">
                </a>
                <div class="card hs-dropdown-menu transition-[opacity,margin] rounded-md duration hs-dropdown-open:opacity-100 opacity-0 mt-2 min-w-max w-[200px] hidden z-[12]" aria-labelledby="hs-dropdown-custom-icon-trigger">
                    <div class="card-body p-0 py-2">
                        <a style="margin-left:10px" href="{{ route('admin.profile.index') }}" class="flex gap-2 items-center font-medium px-4 py-1.5 hover:bg-gray-200 text-gray-400">
                            <i class="ti ti-user text-xl"></i>
                            <p class="text-sm">Thông tin</p>
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                          <button style="margin-left:20px" type="submit" class=" btn btn-primary flex">
    <i class="ti ti-logout"></i>
    Đăng xuất
</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

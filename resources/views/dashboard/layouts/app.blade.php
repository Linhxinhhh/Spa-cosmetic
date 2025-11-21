
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name'))</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('admin/images/logos/favicon.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dashboard/css/theme.css') }}" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{asset('admin/giaodien/css/style.css')}}" rel="stylesheet">
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            color: #1e293b;
        }

        .content-wrapper {
            margin-left: 270px; /* Chiều rộng sidebar */
            padding-top: 160px; /* Tổng chiều cao app-topstrip (60px) + header phụ (80px) */
        }

        main {
            padding: 1rem 1.5rem;
        }

        /* Đảm bảo app-topstrip không bị chồng lấn */
        .app-topstrip {
            height: 60px; /* Đặt chiều cao cố định */
            z-index: 1200;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="@yield('body-class', 'bg-gray-100')">
    <div class="app-topstrip z-40 sticky top-0 py-[15px] px-6 bg-[linear-gradient(90deg,_#0f0533_0%,_#1b0a5c_100%)]">
        <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col md:flex-row items-center gap-4 justify-center">
                <h4 class="text-sm font-normal text-white uppercase font-semibold bg-[linear-gradient(90deg,_#FFFFFF_0%,_#8D70F8_100%)] [-webkit-background-clip:text] [background-clip:text] [-webkit-text-fill-color:transparent]">
                    Lyn Beauty & Spa
                </h4>
            </div>
        </div>
    </div>
    @include('dashboard.layouts.sidebar')
    <div class="content-wrapper">
        @include('dashboard.layouts.header')
        <main>
            @yield('content')
        </main>
        @include('dashboard.layouts.footer')
    </div>
    @stack('scripts')
    <script src="{{ asset('dashboard/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('dashboard/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('dashboard/libs/iconify-icon/dist/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('dashboard/libs/@preline/dropdown/index.js') }}"></script>
    <script src="{{ asset('dashboard/libs/@preline/overlay/index.js') }}"></script>
    <script src="{{ asset('dashboard/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('dashboard/assets/libs/preline/dist/preline.js') }}"></script>
    <script src="{{ asset('dashboard/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/dashboard.js') }}"></script>
    <script src="{{ asset('dashboard/js/products.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
     <script src="{{asset('admin/giaodien/js/main.js')}}"></script>
    @stack('scripts') 
   
</body>
</html>

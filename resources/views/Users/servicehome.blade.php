
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Lyn - Cosmetic & Spa</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{asset('users/giaodien/lib/animate/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('users/giaodien/lib/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{asset('users/giaodien/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- bang gia dich vu-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Template Stylesheet -->
    <link href="{{asset('users/giaodien/css/style.css')}}" rel="stylesheet">
    @stack('styles')
    @push('styles')
<style>
  .product-item{ border-radius:12px; transition:.2s; }
  .product-item:hover{ box-shadow:0 12px 28px rgba(0,0,0,.08); transform:translateY(-2px); }
    .product-text{
    font-family: 'Roboto', sans-serif;
    text-align: center; 
    color: #333;
    }
  .product-thumb{
    position:relative; height:260px; overflow:hidden; border-radius:12px; background:#fff;
    display:flex; align-items:center; justify-content:center;
  }
  .product-thumb img{
    position:absolute; inset:0; margin:auto; max-width:100%; max-height:100%;
    object-fit:contain; object-position:center; transition:opacity .25s ease;
  }
  .product-thumb img.hover-img{ opacity:0; z-index:1; }
  .product-item:hover .product-thumb img.hover-img{ opacity:1; z-index:2; }
  .product-item:hover .product-thumb img.main-img{ opacity:0; }

  .product-thumb .quick-actions{
    position:absolute; left:50%; bottom:12px; transform:translate(-50%,10px);
    display:flex; gap:.5rem; opacity:0; pointer-events:none; z-index:3;
    transition:opacity .2s, transform .2s;
  }
  .product-item:hover .quick-actions{ opacity:1; transform:translate(-50%,0); pointer-events:auto; }

  /* Card chung */
  .result-card{border:0;border-radius:18px;box-shadow:0 8px 22px rgba(0,0,0,.07);transition:.2s}
  .result-card:hover{transform:translateY(-2px);box-shadow:0 12px 28px rgba(0,0,0,.1)}
  /* Sticky footer layout */
.layout { min-height: 100dvh; display: flex; flex-direction: column; }
.layout > main { flex: 1 0 auto; }
.layout > footer { margin-top: auto; }

/* Nếu muốn header dính trên cùng mà không cần JS padding-top */
.site-header { position: sticky; top: 0; z-index: 1100; }

/* Bảo đảm iframe bản đồ không tràn */
.map-wrap iframe { display:block; width:100%; height:100%; }
</style>
@endpush

</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->
      <div class="content-wrapper">
        @include('Users.layouts.header_service')
        <main>
            @yield('content')
        </main>
        @include('Users.layouts.footer')
    </div>

    <!-- Topbar Start -->

  <!-- header -->


  


    <!-- Footer Start -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

 
    <!-- Template Javascript -->
    <script src="{{asset('users/giaodien/js/main.js')}}"></script>
    @stack('scripts') 
</body>

</html>
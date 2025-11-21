{{-- resources/views/partials/header.blade.php --}}
<style>
/* ===== Mega menu ===== */
.mega-wrap { width: min(1100px, 95vw); }
.mega-grid { display:grid; grid-template-columns:280px 1fr; gap:0; min-height:360px; max-height:70vh; }
.mega-left { border-right:1px solid #eef0f3; padding:.75rem; overflow:auto; }
.mega-parent{
  width:100%; border:1px solid transparent; background:transparent; text-align:left; border-radius:.5rem;
  padding:.6rem .75rem; display:flex; align-items:center; justify-content:space-between; color:#000;
}
.mega-parent:hover{ background:#f6f7fb; }
.mega-parent.active{ background:#fff5eb; border-color:#ffe0bf; color:#d67100; }
.text-truncate-2{ display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.mega-right{ padding:1rem; overflow:auto; }
.mega-panel{ display:none; }
.mega-panel.show{ display:block; }
.mega-panel-header{ display:flex; align-items:center; justify-content:space-between; padding:0 0 .5rem; margin-bottom:.75rem; border-bottom:1px dashed #e6e8ec; }
.mega-children-grid{ display:grid; grid-template-columns:repeat(3, minmax(180px,1fr)); gap:.5rem .75rem; }
@media (max-width:1200px){ .mega-children-grid{ grid-template-columns:repeat(2, minmax(160px,1fr)); } }
.mega-child-item{ display:block; padding:.4rem .5rem; border-radius:.35rem; color:#000; text-decoration:none; border:1px solid transparent; }
.mega-child-item:hover{ background:#f6f7fb; border-color:#e7e9ee; color:#0d6efd; text-decoration:none; }
.mega-tabs{ border-bottom:1px solid #eef0f3; }
.mega-content{ padding:.5rem .75rem 1rem; }

/* ===== Account menu ===== */
.account-menu{ position:relative; display:inline-block; }
.account-menu>summary{ list-style:none; cursor:pointer; }
.account-menu>summary::-webkit-details-marker{ display:none; }
.account-menu__button{ display:flex; align-items:center; gap:.5rem; color:#666; }
.account-menu[open] .account-menu__button{ color:#111; }
.account-menu__panel{
  position:absolute; right:0; top:calc(100% + .5rem);
  min-width:220px; background:#fff; border:1px solid #e7e9ee; border-radius:.5rem; padding:.35rem;
  box-shadow:0 10px 30px rgba(0,0,0,.08); z-index:2000;
}
.account-menu__item{ display:flex; align-items:center; gap:.5rem; padding:.5rem .6rem; border-radius:.35rem; color:#222; text-decoration:none; }
.account-menu__item:hover{ background:#f6f7fb; color:#0d6efd; }
.account-menu__hr{ border:0; border-top:1px solid #eef0f3; margin:.35rem 0; }
.account-menu__logout{ width:100%; text-align:left; background:none; border:0; padding:.5rem .6rem; border-radius:.35rem; }
.account-menu__logout:hover{ background:#fff3f3; color:#c62828; }

/* ===== Header common ===== */
.icon-btn{
  width:40px; height:40px; display:inline-flex; align-items:center; justify-content:center;
  border:1px solid #dee2e6; border-radius:50%; color:#f28b00; background:#fff; position:relative;
}
.icon-btn i{ font-size:18px; }
.icon-badge{
  position:absolute; top:-6px; right:-6px; min-width:22px; height:22px; padding:0 6px;
  border-radius:999px; display:inline-flex; align-items:center; justify-content:center; font-size:12px;
}

/* ===== Navbar colors / fix hidden on lg ===== */
@media (min-width:992px){
  .navbar.navbar-expand-lg .navbar-collapse{ display:flex !important; }
}
.navbar .nav-link{ color:#212529 !important; }
.navbar .nav-link.active{ color:#fff !important; }

/* ===== Fixed header & stacking ===== */
.site-header.fixed-top{ position:fixed; top:0; left:0; right:0; width:100%; z-index:1100; background:#fff; }
.site-header .dropdown-menu{ z-index:1110; }
.site-header.fixed-top.is-stuck{ box-shadow:0 6px 18px rgba(0,0,0,.06); }

/* “Danh mục” button color and caret */
.navbar .nav-item.dropdown > .dropdown-toggle{ color:#484848 !important; }
.navbar .nav-item.dropdown > .dropdown-toggle i{ color:#484848 !important; }
.navbar .nav-item.dropdown > .dropdown-toggle::after{ border-top-color:#484848 !important; }
.navbar .nav-item.dropdown > .dropdown-toggle:hover,
.navbar .nav-item.dropdown > .dropdown-toggle:focus{ color:#484848 !important; }
@media (min-width: 992px){
  .navbar-expand-lg .navbar-collapse#navbarCollapse{
    display: flex !important;
    height: auto !important;
    visibility: visible !important;
  }
}

/* Phòng trường hợp có lib khác set .collapse { display:none !important } */
#navbarCollapse.collapse:not(.show){
  /* để Bootstrap quản lý ẩn/hiện trên mobile; không ảnh hưởng desktop vì rule trên đã chặn */
  display: none;
}

/* Màu link cho dễ nhìn trên nền cam */
.navbar .nav-link{ color:#212529 !important; }
.navbar .nav-link.active{ color:#fff !important; }
</style>

<header id="site-header" class="site-header fixed-top bg-white shadow-sm">
  <div>
    {{-- ===== Topbar ===== --}}
    <div class="container-fluid px-5 d-none border-bottom d-lg-block">
      <div class="row gx-0 align-items-center">
        <div class="col-lg-4 text-center text-lg-start mb-lg-0">
          <div class="d-inline-flex align-items-center" style="height:45px;">
            <a href="#" class="text-muted me-2">Trợ giúp</a><small> / </small>
            <a href="#" class="text-muted mx-2">Hỗ trợ</a><small> / </small>
            <a href="#" class="text-muted ms-2">Liên hệ</a>
          </div>
        </div>

        <div class="col-lg-4 text-center d-flex align-items-center justify-content-center">
          <small class="text-dark">Gọi tôi:</small>
          <a href="tel:+84966933624" class="text-muted ms-2">(+84) 966933624</a>
        </div>

        <div class="col-lg-4 d-flex justify-content-end align-items-center">
          <details class="account-menu" role="listbox">
            <summary class="account-menu__button" aria-haspopup="menu">
              <i class="fas fa-user-circle"></i>
              @auth
                <span>Xin chào, <strong>{{ Str::limit(Auth::user()->name, 18) }}</strong></span>
              @else
                <span>Tài khoản</span>
              @endauth
              <i class="fas fa-chevron-down" style="font-size:.8rem;"></i>
            </summary>

            <div class="account-menu__panel" role="menu" aria-label="Account">
              @guest
                <a class="account-menu__item" href="{{ route('users.login') }}">
                  <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </a>
                @if (Route::has('users.register'))
                  <a class="account-menu__item" href="{{ route('users.register') }}">
                    <i class="fas fa-user-plus"></i> Đăng ký
                  </a>
                @endif
                <hr class="account-menu__hr">
                <a class="account-menu__item" href="{{ route('users.home') }}">
                  <i class="fas fa-home"></i> Trang chủ
                </a>
              @else
                <a class="account-menu__item" href="{{ route('users.orders.index') }}"><i class="fas fa-box"></i> Đơn hàng</a>
                <a class="account-menu__item" href="#"><i class="fas fa-heart"></i> Yêu thích</a>
                <a class="account-menu__item" href="#"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>
                <a class="account-menu__item" href="#"><i class="fas fa-bell"></i> Thông báo</a>
                <a class="account-menu__item" href="#"><i class="fas fa-id-card"></i> Trang cá nhân</a>
                <hr class="account-menu__hr">
                <form action="{{ route('users.logout') }}" method="POST">
                  @csrf
                  <button type="submit" class="account-menu__logout">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                  </button>
                </form>
              @endguest
            </div>
          </details>
        </div>
      </div>
    </div>

    {{-- ===== Middle: logo + search + actions ===== --}}
    <div class="container-fluid px-5 py-1 d-none d-lg-block">
      <div class="row gx-0 align-items-center text-center">
        <div class="col-md-4 col-lg-3 text-center text-lg-start">
          <a href="{{ route('users.home') }}" class="navbar-brand p-0 d-inline-flex align-items-center">
            <img src="{{ asset('dashboard/images/logos/logoreplace.png') }}" alt="Logo" style="width:120px; height:100px;">
          </a>
        </div>

        <div class="col-md-4 col-lg-6 text-center">
          <div class="position-relative ps-4">
            <form action="{{ route('users.products.search') }}" method="GET" class="d-flex border rounded-pill">
              <input name="q" id="searchQuery" class="form-control border-0 rounded-pill w-100 py-3"
                     type="text" placeholder="Tìm kiếm sản phẩm của bạn ..." autocomplete="off" />
              <button class="btn btn-primary rounded-pill py-3 px-5" style="border:0" aria-label="Tìm kiếm">
                <i class="fas fa-search"></i>
              </button>
            </form>
          </div>
        </div>

        @php
          $compare = array_values(array_filter(session('compare', []), fn($v) => !is_null($v) && (int)$v > 0));
          $compareCount   = count($compare);
          $wishlistCount  = auth()->check() ? (auth()->user()->wishlist()->count() ?? 0) : 0;
          $headerCartCount = $cart?->items->sum('quantity') ?? 0;
          $headerCartTotal = $cart?->items->sum(fn($i)=> (int)product_final_price($i->product) * (int)$i->quantity) ?? 0;
        @endphp

        <div class="col-md-4 col-lg-3 text-center text-lg-end">
          <div class="d-inline-flex align-items-center gap-2">
            {{-- So sánh --}}
            <a href="{{ route('users.compare.index') }}" class="text-decoration-none" aria-label="So sánh">
              <span class="icon-btn">
                <i class="fas fa-random"></i>
                @if ($compareCount > 0)
                  <span class="badge bg-danger icon-badge">{{ $compareCount }}</span>
                @endif
              </span>
            </a>

            {{-- Yêu thích --}}
            <a href="{{ route('users.wishlist.index') }}" class="text-decoration-none" aria-label="Yêu thích">
              <span class="icon-btn">
                <i class="fas fa-heart"></i>
                @if ($wishlistCount > 0)
                  <span id="wishlistCount" class="badge bg-danger icon-badge">{{ $wishlistCount }}</span>
                @endif
              </span>
            </a>

            {{-- Giỏ hàng --}}
            <a href="{{ route('users.cart.index') }}" class="text-muted d-inline-flex align-items-center text-decoration-none">
              <span class="icon-btn">
                <i class="fas fa-shopping-cart"></i>
                @if ($headerCartCount > 0)
                  <span class="badge bg-danger icon-badge">{{ (int)$headerCartCount }}</span>
                @endif
              </span>
              <span class="text-dark ms-2">{{ number_format((int)$headerCartTotal, 0, ',', '.') }} đ</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- ===== Navbar & Mega menu ===== --}}
    <div class="container-fluid nav-bar p-0">
      <div class="row gx-0 bg-primary px-5 align-items-center">
        {{-- Left: Danh mục --}}
        <div class="col-lg-3 d-none d-lg-block">
          <div class="navbar navbar-light position-relative" style="width:250px;">
            <div class="nav-item dropdown position-static w-100">
              <a id="megaCatsToggle"
                 class="btn w-100 text-start text-black d-flex align-items-center justify-content-between dropdown-toggle"
                 data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <span class="fw-semibold"><i class="fas fa-bars me-2"></i> Danh mục</span>
              </a>

              <div class="dropdown-menu border-0 shadow mega-wrap p-0">
                <div class="mega-tabs px-3 pt-3">
                  <ul class="nav nav-pills" id="catTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="tab-products-tab" data-bs-toggle="pill"
                              data-bs-target="#tab-products" type="button" role="tab">Sản phẩm</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="tab-services-tab" data-bs-toggle="pill"
                              data-bs-target="#tab-services" type="button" role="tab">Dịch vụ</button>
                    </li>
                  </ul>
                </div>

                <div class="tab-content mega-content">
                  {{-- ===== Tab Sản phẩm ===== --}}
                  <div class="tab-pane fade show active" id="tab-products" role="tabpanel">
                    <div class="mega-grid" aria-live="polite">
                      <aside class="mega-left" id="parent-list-products">
                        @forelse($productParents ?? [] as $p)
                          <button type="button"
                                  class="mega-parent {{ $loop->first ? 'active' : '' }}"
                                  data-scope="products"
                                  data-target="children-products-{{ $p->category_id }}"
                                  data-title="{{ $p->category_name }}"
                                  aria-controls="children-products-{{ $p->category_id }}">
                            <span class="text-truncate-2">{{ $p->category_name }}</span>
                            <i class="fas fa-chevron-right ms-2"></i>
                          </button>
                        @empty
                          <div class="text-muted small p-3">Chưa có danh mục sản phẩm.</div>
                        @endforelse
                      </aside>

                      <section class="mega-right">
                        @forelse($productParents ?? [] as $p)
                          <div class="mega-panel {{ $loop->first ? 'show' : '' }}" id="children-products-{{ $p->category_id }}">
                            <div class="mega-panel-header">
                              <h6 class="mb-0">{{ $p->category_name }}</h6>
                            </div>
                            @if($p->children->isEmpty())
                              <div class="text-muted small p-3">Chưa có danh mục con.</div>
                            @else
                              <div class="mega-children-grid">
                                @foreach($p->children as $c)
                                  <a class="mega-child-item"
                                     href="{{ route('users.products.byCategory', ['category' => $c->slug]) }}">
                                    <span class="text-truncate-2">{{ $c->category_name }}</span>
                                  </a>
                                @endforeach
                              </div>
                            @endif
                          </div>
                        @empty @endforelse
                      </section>
                    </div>
                  </div>

                  {{-- ===== Tab Dịch vụ ===== --}}
                  <div class="tab-pane fade" id="tab-services" role="tabpanel">
                    <div class="mega-grid" aria-live="polite">
                      <aside class="mega-left" id="parent-list-services">
                        @forelse($serviceParents ?? [] as $p)
                          <button type="button"
                                  class="mega-parent {{ $loop->first ? 'active' : '' }}"
                                  data-scope="services"
                                  data-target="children-services-{{ $p->category_id }}"
                                  data-title="{{ $p->category_name }}"
                                  aria-controls="children-services-{{ $p->category_id }}">
                            <span class="text-truncate-2">{{ $p->category_name }}</span>
                            <i class="fas fa-chevron-right ms-2"></i>
                          </button>
                        @empty
                          <div class="text-muted small p-3">Chưa có danh mục dịch vụ.</div>
                        @endforelse
                      </aside>

                      <section class="mega-right">
                        @forelse($serviceParents ?? [] as $p)
                          <div class="mega-panel {{ $loop->first ? 'show' : '' }}" id="children-services-{{ $p->category_id }}">
                            <div class="mega-panel-header">
                              <h6 class="mb-0">{{ $p->category_name }}</h6>
                            </div>
                            @if($p->children->isEmpty())
                              <div class="text-muted small p-3">Chưa có danh mục con.</div>
                            @else
                              <div class="mega-children-grid">
                                @foreach($p->children as $c)
                                  <a class="mega-child-item" href="{{ url('/users?scat='.$c->slug) }}">
                                    <span class="text-truncate-2">{{ $c->category_name }}</span>
                                  </a>
                                @endforeach
                              </div>
                            @endif
                          </div>
                        @empty @endforelse
                      </section>
                    </div>
                  </div>
                </div>
              </div> <!-- /dropdown-menu -->
            </div>
          </div>
        </div>

        {{-- Right: main nav --}}
        <div class="col-12 col-lg-9">
          <nav class="navbar navbar-expand-lg navbar-light bg-primary">
            <a href="{{ route('users.home') }}" class="navbar-brand d-block d-lg-none">
              <img src="{{ asset('dashboard/images/logos/logoreplace.png') }}" alt="Logo" style="width:120px; height:100px;">
            </a>

            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
              <span class="fas fa-bars"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
              <ul class="navbar-nav ms-auto py-0">
                <li class="nav-item">
                  <a href="{{ route('users.home') }}"
                     class="nav-link {{ request()->routeIs('users.home') ? 'active text-white' : '' }}">Trang chủ</a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('users.products.index') }}"
                     class="nav-link {{ request()->routeIs('users.products.*') ? 'active text-white' : '' }}">Cửa hàng</a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('users.services.index') }}"
                     class="nav-link {{ request()->routeIs('users.services.*') ? 'active text-white' : '' }}">Dịch vụ</a>
                </li>

                <li class="nav-item dropdown">
                  <a href="#" class="nav-link dropdown-toggle {{ request()->is('bestseller.html') ? 'active text-white' : '' }}"
                     data-bs-toggle="dropdown">Trang</a>
                  <div class="dropdown-menu m-0">
                    <a href="{{ route('users.products.index') }}" class="dropdown-item">Sản phẩm bán chạy</a>
                    <a href="{{ route('users.services.index') }}" class="dropdown-item">Dịch vụ nổi bật</a>
                    <a href="{{ route('users.cart.index') }}" class="dropdown-item">Giỏ hàng</a>
                    <a href="{{ route('users.checkout.show') }}" class="dropdown-item">Thanh toán</a>
                    <a href="" class="dropdown-item">Trang 404</a>
                  </div>
                </li>

                <li class="nav-item me-2">
                  <a href="{{ route('users.contact.index') }}"
                     class="nav-link ">Liên hệ</a>
                </li>
              </ul>

              <a href="tel:+84966933624" class="btn btn-secondary rounded-pill py-2 px-4 px-lg-3 mb-3 mb-md-3 mb-lg-0">
                <i class="fas fa-mobile-alt me-2"></i> +84966933624
              </a>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </div>
</header>

<script>
/* ===== Mega menu interactions ===== */
(function(){
  // click parent -> show child panel in scope
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.mega-parent');
    if (!btn) return;
    const scope = btn.getAttribute('data-scope');
    const targetId = btn.getAttribute('data-target');

    document.querySelectorAll('.mega-parent[data-scope="'+scope+'"]').forEach(x => x.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('#tab-'+scope+' .mega-panel').forEach(p => p.classList.remove('show'));
    const panel = document.getElementById(targetId);
    if (panel) panel.classList.add('show');
  });

  // open dropdown -> auto select first parent of current tab
  const megaToggle = document.getElementById('megaCatsToggle');
  megaToggle?.addEventListener('shown.bs.dropdown', () => {
    const activeTab = document.querySelector('#catTabs .nav-link.active');
    if (!activeTab) return;
    const tabId = activeTab.getAttribute('data-bs-target'); // #tab-products | #tab-services
    const scope = tabId.includes('products') ? 'products' : 'services';
    const firstParent = document.querySelector('#tab-'+scope+' .mega-parent');
    firstParent?.click();
  });

  // when switch tab -> reset & select first parent of that tab
  document.querySelectorAll('#catTabs [data-bs-toggle="pill"]').forEach(t => {
    t.addEventListener('shown.bs.tab', (ev) => {
      const tabId = ev.target.getAttribute('data-bs-target');
      const scope = tabId.includes('products') ? 'products' : 'services';
      document.querySelectorAll('#tab-'+scope+' .mega-parent').forEach(x => x.classList.remove('active'));
      document.querySelectorAll('#tab-'+scope+' .mega-panel').forEach(p => p.classList.remove('show'));
      const firstParent = document.querySelector('#tab-'+scope+' .mega-parent');
      firstParent?.click();
    });
  });
})();
</script>

<script>
/* ===== Pad body for fixed header & sticky shadow ===== */
(function(){
  const h = document.getElementById('site-header');
  function syncPad(){
    document.body.style.paddingTop = (h?.offsetHeight || 0) + 'px';
    h?.classList.toggle('is-stuck', (window.scrollY || window.pageYOffset) > 8);
  }
  window.addEventListener('load', syncPad, { once:true });
  window.addEventListener('resize', syncPad);
  window.addEventListener('orientationchange', syncPad);
  window.addEventListener('scroll', syncPad, { passive:true });
})();
</script>

<script>
/* ===== Close account <details> when clicking outside / ESC ===== */
document.addEventListener('click', (e) => {
  document.querySelectorAll('.account-menu[open]').forEach(dd => {
    if(!dd.contains(e.target)) dd.removeAttribute('open');
  });
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.account-menu[open]').forEach(dd => dd.removeAttribute('open'));
  }
});
</script>

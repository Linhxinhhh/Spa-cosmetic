
<style>
    /* Mega chung (giữ như bạn đã có) */
.mega-wrap { width: min(1100px, 95vw); }
.mega-grid{display:grid;grid-template-columns:280px 1fr;gap:0;min-height:340px;max-height:70vh}
.mega-left{border-right:1px solid #eef0f3;padding:.75rem;overflow:auto}
.mega-right{padding:1rem;overflow:auto}
.mega-parent{width:100%;border:1px solid transparent;background:transparent;text-align:left;border-radius:.5rem;padding:.6rem .75rem;display:flex;align-items:center;justify-content:space-between;color:#111}
.mega-parent:hover{background:#f6f7fb}
.mega-parent.active{background:#fff5eb;border-color:#ffe0bf;color:#d67100}
.mega-panel{display:none}.mega-panel.show{display:block}
.mega-panel-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:.4rem;
  flex-wrap:wrap;           /* nếu quá hẹp, cho phép xuống dòng gọn gàng */
}
.mega-panel-header h6{
  margin:0;
  flex:1 1 auto;            /* phần tiêu đề chiếm phần còn lại */
  min-width:0;              /* cho phép text-truncate */
  overflow:hidden;
  text-overflow:ellipsis;   /* ... khi quá dài */
  white-space:nowrap;
}
.mega-panel-header a{
  flex:0 0 auto;            /* link giữ kích thước tự nhiên, không bị co */
  white-space:nowrap;       /* không xuống dòng thành 2 hàng */
}
.mega-children-grid{display:grid;grid-template-columns:repeat(3,minmax(180px,1fr));gap:.5rem .75rem}
.mega-child-item{display:block;padding:.4rem .5rem;border-radius:.35rem;color:#111;text-decoration:none;border:1px solid transparent}
.mega-child-item:hover{background:#f6f7fb;border-color:#e7e9ee;color:#0d6efd;text-decoration:none}
.text-truncate-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
@media (max-width:1200px){.mega-children-grid{grid-template-columns:repeat(2,minmax(160px,1fr))}}

/* header */
.svc-header.fixed-top{position:fixed;top:0;left:0;right:0;z-index:1100;background:#fff}
.svc-header .dropdown-menu{z-index:1110}

</style>
<style>
  /* Căn giữa & chia đều các mục menu giữa khi >=992px */
@media (min-width: 992px){
  .navbar .navbar-collapse{
    display: flex !important;
    justify-content: center;   /* <-- căn giữa */
     gap: 3rem; 
  }
  .nav-center{
    display: flex;
    gap: .5rem;
  }
  .nav-center .nav-link{
    color: #fff;
    text-align: center;
    padding: .75rem 1rem;
  }


}
</style>


<header id="svc-header" class="svc-header fixed-top bg-white shadow-sm">


  {{-- Middle: logo + search dịch vụ + tài khoản --}}
  <div class="container-fluid px-4 py-2">
    <div class="row g-3 align-items-center">
      <div class="col-6 col-lg-3">
        <a href="{{ route('users.home') }}" class="navbar-brand p-0 d-inline-flex align-items-center">
          <img src="{{ asset('dashboard/images/logos/logoreplace.png') }}" alt="Logo" style="height:64px">
        </a>
      </div>

      <div class="col-12 col-lg-6 order-3 order-lg-2">
        <form action="{{ route('users.services.index') }}" method="GET" class="d-flex border rounded-pill overflow-hidden">
          <input name="q" class="form-control border-0 rounded-0 w-100 py-3"
                 type="text" placeholder="Tìm dịch vụ (ví dụ: triệt lông, chăm sóc da)..." value="{{ request('q') }}" />
          <button class="btn btn-primary rounded-pill px-4" style="border:0" aria-label="Tìm kiếm">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>

      <div class="col-6 col-lg-3 d-flex justify-content-end order-2 order-lg-3">
        <a href="{{ route('users.booking.create') }}" class="btn btn-warning rounded-pill px-4 fw-semibold">
          <i class="fa fa-calendar-check me-2"></i>Đặt lịch ngay
        </a>
      </div>
      
    </div>
    
  </div>

  {{-- Nav: Mega menu DỊCH VỤ --}}
  <div class="bg-primary">
    <div class="container-fluid px-3">
      <nav class="navbar navbar-expand-lg navbar-light bg-primary p-0">
        <button class="navbar-toggler ms-auto text-white" type="button" data-bs-toggle="collapse" data-bs-target="#svcNav">
          <span class="fa fa-bars"></span>
        </button>

<div class="collapse navbar-collapse" id="svcNav">
  <ul class="navbar-nav nav-center my-2 my-lg-0">
    <li class="nav-item">
      <a href="{{ route('users.services.index') }}"
         class="nav-link {{ request()->routeIs('users.services.*') ? 'fw-bold' : '' }}">
        LYN SPA
      </a>
    </li>

    <li class="nav-item dropdown position-static">
      <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">DANH MỤC</a>
      <div class="dropdown-menu border-0 shadow mega-wrap p-0">
        <div class="mega-grid">
          {{-- Trái: CHA --}}
          <aside class="mega-left" id="svc-parent-list">
            @forelse($serviceParents ?? [] as $p)
              <button type="button"
                      class="mega-parent {{ $loop->first ? 'active' : '' }}"
                      data-target="svc-children-{{ $p->category_id }}">
                <span class="text-truncate-2">{{ $p->category_name }}</span>
                <i class="fa fa-chevron-right ms-2"></i>
              </button>
            @empty
              <div class="text-muted small p-3">Chưa có danh mục dịch vụ.</div>
            @endforelse
          </aside>

          {{-- Phải: CON --}}
          <section class="mega-right">
            @forelse($serviceParents ?? [] as $p)
              <div class="mega-panel {{ $loop->first ? 'show' : '' }}" id="svc-children-{{ $p->category_id }}">
                <div class="mega-panel-header">
                  <h6 class="mb-0">{{ $p->category_name }}</h6>
                  
                </div>

                @if($p->children->isEmpty())
                  <div class="text-muted small p-3">Chưa có danh mục con.</div>
                @else
                  <div class="mega-children-grid">
                    @foreach($p->children as $c)
                      <a class="mega-child-item"
                         href="{{ route('users.services.index', ['category_id' => $c->category_id]) }}">
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
    </li>

    <li class="nav-item">
      <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('users.about.index')}}">GIỚI THIỆU</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('dich-vu*') ? 'active' : '' }}" href="{{ route('users.services.index') }}">DỊCH VỤ</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('bang-gia') ? 'active' : '' }}" href="{{ route('users.pricelist.index') }}">BẢNG GIÁ</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('cam-nang') ? 'active' : '' }}" href="{{ route('users.guides.index') }}">CẨM NANG</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('lien-he') ? 'active' : '' }}" href="{{ route('users.contact.index') }}">LIÊN HỆ</a>
    </li>
    @auth
<li  class="nav-item dropdown">
  <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
    <div class="user-avatar me-2">
      <i class="fas fa-user-circle text-secondary fs-4"></i>
    </div>
    <div class="d-none d-md-block">
      <span class="fw-semibold text-dark">{{ Auth::user()->name ?? 'Tài khoản' }}</span>
    </div>
  </a>
  <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3" style="min-width: 240px;">
    <li class="dropdown-item py-3 border-bottom">
      <div class="d-flex align-items-center">
        <div class="user-avatar me-3">
          <i class="fas fa-user-circle text-primary fs-3"></i>
        </div>
        <div>
          <h6 class="mb-0 fw-semibold text-dark">{{ Auth::user()->name ?? 'Người dùng' }}</h6>
          <small class="text-muted">{{ Auth::user()->email ?? 'user@example.com' }}</small>
        </div>
      </div>
    </li>
    <li><hr class="dropdown-divider my-0"></li>

     <li>
      <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('users.booking.index') }}">
         <i class="fas fa-calendar-check me-2 text-primary fs-6"></i>
        <span>Lịch hẹn của tôi</span>
      </a>
    </li>
    <li>
      <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('users.customer.sessions.index') }}">
        <i class="fas fa-calendar-alt me-3 text-primary"></i>
        <span>Lịch trị liệu của tôi</span>
      </a>
    </li>
    <li>
      <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('users.treatments.index') }}">
        <i class="fas fa-heart me-3 text-danger"></i>
        <span>Liệu trình của tôi</span>
      </a>
    </li>
    <li><hr class="dropdown-divider my-0"></li>
    <li>
      <form method="POST" action="{{ route('users.logout') }}" class="w-100">
        @csrf
        <button class="dropdown-item d-flex align-items-center py-2 text-danger w-100 text-start border-0 bg-transparent">
          <i class="fas fa-sign-out-alt me-3"></i>
          <span>Đăng xuất</span>
        </button>
      </form>
    </li>
  </ul>
</li>
@endauth

@guest
<li class="nav-item">
  <a href="{{ route('users.login') }}" class="nav-link d-flex align-items-center px-3 py-2 rounded-pill btn btn-outline-primary border-2 fw-semibold transition-all">
    <i class="fas fa-user me-2"></i>
    <span>Đăng nhập</span>
  </a>
</li>
@endguest

<style>
/* User Avatar Styling */
.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
  transition: all 0.2s ease;
}

.user-avatar:hover {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  transform: scale(1.05);
}

/* Dropdown Enhancements */
.dropdown-menu {
  animation: slideDown 0.2s ease-out;
  backdrop-filter: blur(10px);
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}



.dropdown-item i {
  width: 20px;
  text-align: center;
  opacity: 0.8;
  transition: opacity 0.2s ease;
}



/* Guest Login Button */
.btn-outline-primary:hover {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  border-color: #2563eb;
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
  .nav-link.dropdown-toggle .d-none.d-md-block {
    display: none !important;
  }
  
  .user-avatar {
    width: 32px;
    height: 32px;
  }
  
  .user-avatar i {
    font-size: 1.2rem;
  }
}
</style>
  </ul>

</div>


      </nav>
    </div>
  </div>
</header>

{{-- đệm tránh che nội dung do fixed-top --}}
<script>
(function(){
  const h = document.getElementById('svc-header');
  function syncPad(){ document.body.style.paddingTop = (h?.offsetHeight || 0) + 'px'; }
  window.addEventListener('load', syncPad);
  window.addEventListener('resize', syncPad);
})();

</script>
<script>
document.addEventListener('click', function(e){
  const btn = e.target.closest('.mega-parent');
  if(!btn) return;
  const id = btn.getAttribute('data-target');

  // reset
  document.querySelectorAll('#svc-parent-list .mega-parent').forEach(x=>x.classList.remove('active'));
  document.querySelectorAll('.mega-panel').forEach(p=>p.classList.remove('show'));

  // set
  btn.classList.add('active');
  document.getElementById(id)?.classList.add('show');
});

document.addEventListener('shown.bs.dropdown', function(ev){
  const menu = ev.target.closest('.dropdown');
  if(!menu) return;
  // auto chọn mục đầu khi mở
  const first = menu.querySelector('#svc-parent-list .mega-parent');
  first?.click();
});
</script>


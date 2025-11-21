@extends('users.servicehome')

@section('title', 'Liên hệ')

@section('content')
<style>
  .contact-hero{
    background: linear-gradient(180deg, #fff7ed, #fff);
    border-bottom: 1px solid #f1f3f5;
  }
  .contact-card{
    height: 100%;
    border: 1px solid #eef0f3;
    border-radius: .75rem;
    padding: 1rem;
    transition: box-shadow .2s ease, transform .2s ease;
    background: #fff;
  }
  .contact-card:hover{ box-shadow: 0 10px 30px rgba(0,0,0,.06); transform: translateY(-2px); }
  .contact-icon{
    width: 44px; height: 44px; border-radius: 999px;
    display:inline-flex; align-items:center; justify-content:center;
    background:#fff5eb; color:#d67100; border:1px solid #ffe2c4;
    margin-right: .5rem;
  }
  .contact-form .form-control:focus{ box-shadow: none; border-color: #ffb44c; }
  .required::after{ content:" *"; color:#e03131; }
  .map-wrap{ border: 1px solid #eef0f3; border-radius: .75rem; overflow: hidden; }
  .breadcrumb{ list-style:none; margin:0; padding:0; }
  .breadcrumb .breadcrumb-item + .breadcrumb-item::before{ content:'›'; color:#adb5bd; padding:0 .5rem; }
  .social a{ display:inline-flex; width:40px; height:40px; align-items:center; justify-content:center;
    border:1px solid #e9ecef; border-radius:50%; background:#fff; color:#495057; margin-right:.35rem; }
  .social a:hover{ color:#0d6efd; border-color:#dbe0e6; }
  /* Chiều cao footer ước lượng (đổi số cho đúng site của bạn) */
:root{ --footer-h: 120px; }

/* Chừa chỗ dưới để footer fixed không đè nội dung */
.contact-page{ padding-bottom: calc(var(--footer-h) + 24px); }

/* Nếu footer đang cố định, hạ nó về static để tránh đè */
.site-footer, footer.fixed-bottom{
  position: static !important;
}

/* Đảm bảo iframe map hiển thị gọn, không tràn */
.map-wrap iframe{ display:block; width:100%; height:100%; }
 :root{ --footer-h: 120px; }
  .contact-page{ padding-bottom: calc(var(--footer-h) + 24px); }
  .site-footer, footer.fixed-bottom{ position: static !important; }
  .map-wrap iframe{ display:block; width:100%; height:100%; }
    .cat-link
  {
    color: #3f3f46;
  }
  .cat-link:hover
  {
    color: gray; 
  }
  .breadcrumb .breadcrumb-item + .breadcrumb-item::before{
  content: '›';
  color: #adb5bd;          /* xám nhạt */
  padding: 0 .5rem;
}
  /* Làm dấu phân cách TO và ĐẬM */
.breadcrumb-chevron .breadcrumb-item + .breadcrumb-item::before{
  font-size: 22px;        /* <— tăng/giảm tùy ý (18–26px) */
  font-weight: 500;
  line-height: 1;
  color: #6c757d;         /* màu xám; đổi sang #f97316 nếu muốn cam */
  position: relative;
  top: -1px;              /* chỉnh viền dọc cho cân */
  padding-right: .75rem;  /* nới khoảng cách */
}

/* Nới khoảng bên trái item sau */
.breadcrumb-chevron .breadcrumb-item + .breadcrumb-item{}
</style>

<div class="contact-hero py-3">
  <div class="container-xl">
    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb breadcrumb-chevron">
           <li class="breadcrumb-item"><a href="{{ route('users.home') }}" class="cat-link">Trang chủ</a></li>
        <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
  </ol>
</nav>

    <div class="d-flex align-items-center gap-3 mt-2">
      <div class="contact-icon"><i class="fas fa-envelope-open-text"></i></div>
      <div>
        <h1 class="h4 mb-0">Liên hệ với chúng tôi</h1>
        <small class="text-muted">Gửi thắc mắc, đặt lịch tư vấn, hoặc góp ý để chúng tôi phục vụ tốt hơn.</small>
      </div>
    </div>
  </div>
</div>

<div class="contact-page py-4">
  <div class="container-xl">

    {{-- Flash message --}}
    @if (session('status'))
      <div class="alert alert-success border-0 shadow-sm">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
      </div>
    @endif

    {{-- 1) THÔNG TIN LIÊN HỆ NGẮN --}}
    <div class="row g-3 mb-4">
      <div class="col-md-6 col-lg-3">
        <div class="contact-card d-flex">
          <div class="contact-icon"><i class="fas fa-phone"></i></div>
          <div>
            <div class="small text-muted">Điện thoại</div>
            <a href="tel:+84966933624" class="text-decoration-none fw-semibold">(+84) 966 933 624</a>
            <div class="text-muted small">8:00–20:00 (T2–CN)</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="contact-card d-flex">
          <div class="contact-icon"><i class="fas fa-envelope"></i></div>
          <div>
            <div class="small text-muted">Email</div>
            <a href="mailto:hello@yourspa.vn" class="text-decoration-none fw-semibold">Lyn@cosmeticspa.vn</a>
            <div class="text-muted small">Phản hồi trong 24h</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="contact-card d-flex">
          <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div>
            <div class="small text-muted">Địa chỉ</div>
            <div class="fw-semibold">233 Lê Hồng Phong, Phú Lợi, TP.HCM</div>
            <div class="text-muted small">Xem bản đồ bên phải</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="contact-card d-flex">
          <div class="contact-icon"><i class="fas fa-clock"></i></div>
          <div>
            <div class="small text-muted">Giờ làm việc</div>
            <div class="fw-semibold">T2–CN: 8:00–20:00</div>
            <div class="text-muted small">Nhận lịch ngoài giờ: Có</div>
          </div>
        </div>
      </div>
    </div>

    {{-- 2) FORM + MAP --}}
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="contact-card contact-form">
          <h2 class="h5 mb-3">Gửi yêu cầu</h2>

          <form action="{{ route('users.contact.submit') }}" method="POST" novalidate>
            @csrf

            {{-- Honeypot chống spam --}}
            <input type="text" name="hp" class="d-none" tabindex="-1" autocomplete="off">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label required">Họ và tên</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Nguyễn Văn A" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label required">Số điện thoại</label>
                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}" placeholder="09xx xxx xxx" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="ban@vidu.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Chủ đề</label>
                <select name="subject" class="form-select @error('subject') is-invalid @enderror">
                  <option value="" selected>— Chọn chủ đề —</option>
                  <option @selected(old('subject')=='Tư vấn dịch vụ')>Tư vấn dịch vụ</option>
                  <option @selected(old('subject')=='Đặt lịch')>Đặt lịch</option>
                  <option @selected(old('subject')=='Tư vấn dịch vụ')>Hỏi đáp</option>
                  <option @selected(old('subject')=='Khiếu nại/Góp ý')>Khiếu nại/Góp ý</option>
                  <option @selected(old('subject')=='Khác')>Khác</option>
                </select>
                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12">
                <label class="form-label required">Nội dung</label>
                <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror"
                          placeholder="Mô tả nhu cầu của bạn..." required>{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              {{-- reCAPTCHA (nếu dùng) --}}
              {{-- <div class="col-12">
                   {!! NoCaptcha::display() !!}
                   @error('g-recaptcha-response')<div class="text-danger small">{{ $message }}</div>@enderror
                 </div> --}}

              <div class="col-12 d-flex align-items-center justify-content-between">
                <div class="text-muted small">
                  Bằng cách gửi, bạn đồng ý với <a href="">Điều khoản</a> của chúng tôi.
                </div>
                <button class="btn btn-primary px-4">
                  <i class="fas fa-paper-plane me-1"></i> Gửi liên hệ
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="mt-3 small text-muted">
          <div class="social mt-2">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="Tiktok"><i class="fab fa-tiktok"></i></a>
            <a href="#" aria-label="Zalo"><i class="fas fa-comment"></i></a>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="map-wrap ratio ratio-4x3">
          {{-- Thay src bằng embed map của bạn --}}
<iframe
  src="https://www.google.com/maps?q=10.9824488,106.6789682&z=16&hl=vi&output=embed"
  style="border:0;" allowfullscreen="" loading="lazy"
  referrerpolicy="no-referrer-when-downgrade">
</iframe>
        </div>

        
      </div>
    </div>

  </div>
</div>
@endsection

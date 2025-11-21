<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập - AMU Beauty & Spa</title>
  <style>
    :root {
      --primary: #e8b4b8;
      --primary-dark: #d1969b;
      --secondary: #f4e6e7;
      --accent: #c89a9d;
      --bg-gradient-1: #fdf7f7;
      --bg-gradient-2: #f8f0f2;
      --text-primary: #5a4a4b;
      --text-secondary: #8b7575;
      --text-muted: #b8a5a6;
      --white: #ffffff;
      --border: #e6d4d6;
      --shadow-light: rgba(232, 180, 184, 0.15);
      --shadow-medium: rgba(200, 154, 157, 0.25);
      --error: #d4756b;
      --success: #7db899;
      --gold: #dab892;
      --rose-gold: #e8c4a0;
    }

    * {
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body.auth-body {
      background: linear-gradient(135deg, var(--bg-gradient-1) 0%, var(--bg-gradient-2) 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
    }

    body.auth-body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(circle at 20% 20%, rgba(232, 180, 184, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(248, 240, 242, 0.15) 0%, transparent 50%);
      pointer-events: none;
    }

    .auth-card {
      max-width: 1200px;
      width: 100%;
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      background: var(--white);
      border-radius: 24px;
      box-shadow: 
        0 20px 40px var(--shadow-light),
        0 8px 24px var(--shadow-medium);
      overflow: hidden;
      border: 1px solid var(--border);
      position: relative;
      z-index: 1;
    }

    .auth-left {
      position: relative;
      min-height: 500px;
     background: url('/images/background/bgregister.png') no-repeat center center;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
    }

    .auth-left::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
    }

    .brand {
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
      z-index: 2;
    }

    .logo {
      font-weight: 700;
      font-size: 32px;
      color: var(--white);
      letter-spacing: 0.1em;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .back-link {
      background: rgba(255, 255, 255, 0.2);
      padding: 12px 20px;
      border-radius: 50px;
      color: var(--white);
      border: 1px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .back-link:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: translateY(-2px);
    }

        .hero-copy {
      color: var(--white);
      padding: 20px 0;
      position: relative;
      z-index: 2;
      animation: fadeIn 1s ease-out forwards;
    }

    .hero-copy h3 {
      font-size: 36px;
      line-height: 1.3;
      margin: 0 0 20px;
      font-weight: 600;
      text-shadow: 0 2px 8px rgba(0,0,0,0.2);
      animation: pulse 2s infinite ease-in-out;
       /* Đổi lại màu trắng để nổi bật trên lớp phủ tối */
    }

    .hero-copy p {
      font-size: 18px;
      margin: 0 0 30px;
      opacity: 0.9;
      line-height: 1.6;
      transition: color 0.3s ease;
      /* Đổi lại màu trắng để đồng bộ */
    }

    .hero-copy p:hover {
      color: var(--rose-gold); /* Chuyển màu khi hover */
    }

    .dots {
      display: flex;
      gap: 8px;
      margin-top: 20px;
    }

    .dots span {
      width: 32px;
      height: 4px;
      border-radius: 4px;
      background: rgba(255, 255, 255, 0.4);
      transition: all 0.3s ease;
    }

    .dots .active {
      background: var(--white);
      width: 40px;
    }

    .auth-right {
      padding: 60px 50px;
      background: var(--white);
      position: relative;
    }

    .title {
      
      margin: 0 0 8px;
      font-size: 42px;
      color: var(--text-primary);
      font-weight: 600;
      letter-spacing: -0.02em;
    }

    .subtitle {
      margin: 0 0 40px;
      color: var(--text-secondary);
      font-size: 16px;
    }

    .subtitle a {
      color: var(--primary);
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .subtitle a:hover {
      color: var(--primary-dark);
    }

    .alert {
      padding: 16px 20px;
      border-radius: 16px;
      margin-bottom: 24px;
      border: 1px solid;
      font-weight: 500;
    }

    .alert.success {
      border-color: rgba(125, 184, 153, 0.3);
      background: rgba(125, 184, 153, 0.1);
      color: var(--success);
    }

    .alert.error {
      border-color: rgba(212, 117, 107, 0.3);
      background: rgba(212, 117, 107, 0.1);
      color: var(--error);
    }

    .form {
      display: block;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
      position: relative;
      margin-bottom: 24px;
    }

    .form-group label {
      color: var(--text-primary);
      font-size: 14px;
      font-weight: 600;
    }

    .form-group input {
      background: var(--secondary);
      border: 2px solid transparent;
      color: var(--text-primary);
      border-radius: 16px;
      padding: 16px 20px;
      font-size: 16px;
      outline: none;
      transition: all 0.3s ease;
    }

    .form-group input:focus {
      border-color: var(--primary);
      background: var(--white);
      box-shadow: 0 0 0 4px var(--shadow-light);
    }

    .form-group input:hover {
      border-color: var(--border);
    }

    .toggle {
      position: absolute;
      right: 16px;
      top: 38px;
      border: none;
      background: transparent;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 18px;
      padding: 4px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .toggle:hover {
      background: var(--secondary);
      color: var(--text-primary);
    }

    .field-error {
      color: var(--error);
      font-size: 12px;
      font-weight: 500;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      min-height: 54px;
      border-radius: 16px;
      padding: 0 24px;
      border: 2px solid;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      width: 100%;
    }

    .btn.primary {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      border-color: transparent;
      color: var(--white);
      box-shadow: 0 4px 16px var(--shadow-light);
    }

    .btn.primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px var(--shadow-medium);
    }

    .btn.outline {
      background: var(--white);
      border-color: var(--border);
      color: var(--text-primary);
    }

    .btn.outline:hover {
      border-color: var(--primary);
      background: var(--secondary);
    }

    .btn img {
      width: 20px;
      height: 20px;
    }

    .divider {
      display: flex;
      align-items: center;
      gap: 16px;
      color: var(--text-muted);
      margin: 32px 0;
      font-size: 14px;
    }

    .divider:before,
    .divider:after {
      content: "";
      height: 1px;
      background: var(--border);
      flex: 1;
    }

    .socials {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 1024px) {
      .auth-card {
        grid-template-columns: 1fr;
        max-width: 500px;
      }
      
      .auth-left {
        min-height: 280px;
      }
      
      .hero-copy h3 {
        font-size: 28px;
      }
      
      .auth-right {
        padding: 40px 30px;
      }
    }

    @media (max-width: 768px) {
      .socials {
        grid-template-columns: 1fr;
      }
      
      .title {
        font-size: 32px;
      }
    }
  </style>
</head>
<body class="auth-body">
  <div class="auth-card">
    <div class="auth-left">
      <div class="brand">
        <span style="color: #5a4a4b" class="logo"></span>
        <a style="margin-right:15px;" href="{{ url('/') }}" class="back-link">Quay lại trang →</a>
      </div>
      <div style="margin-left:20px;margin-bottom:10px;"   class="hero-copy">
        <h3>Chào mừng bạn trở lại<br></h3>
        <p>Đăng nhập ngay để trải nghiệm dịch vụ đẳng cấp<br> từ Lyn Cosmetic & Spa</p>
        <div class="dots">
          <span></span><span class="active"></span><span></span>
        </div>
      </div>
    </div>

    <div class="auth-right">
      <h1 style="text-align: center" class="title">ĐĂNG NHẬP</h1>
      <p style="text-align: center" class="subtitle">
        Bạn chưa có tài khoản?
        <a href="{{ route('users.register') }}">Đăng ký </a>
      </p>

      @if ($errors->any())
        <div class="alert error">{{ $errors->first() }}</div>
      @endif

      <form class="form" method="POST" action="{{ route('users.login.post') }}" novalidate>
        @csrf

        <div class="form-group">
          <label for="email">Đia chỉ Email:</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Vui lòng nhập email" />
          @error('email')<small class="field-error">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label for="password">Mật khẩu:</label>
          <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Vui lòng nhập mật khẩu" />
          <button type="button" class="toggle" data-toggle="#password">👁</button>
          @error('password')<small class="field-error">{{ $message }}</small>@enderror
        </div>

        <button  class="btn primary" type="submit">Đăng nhập</button>

        <div class="divider"><span>Hoặc đăng nhập với</span></div>
        <div class="socials">
          <button type="button" class="btn outline">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="" />
            Google
          </button>
          <button type="button" class="btn outline">
            <img src="{{asset('images/svgs/apple.png')}}" alt="" />
            Apple
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Toggle show/hide password
    document.querySelectorAll('.toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        const target = document.querySelector(btn.dataset.toggle);
        if (!target) return;
        
        if (target.type === 'password') {
          target.type = 'text';
          btn.textContent = '🙈';
        } else {
          target.type = 'password';
          btn.textContent = '👁';
        }
      });
    });

    // Add smooth transitions to form inputs
    document.querySelectorAll('input').forEach(input => {
      input.addEventListener('focus', () => {
        input.parentElement.style.transform = 'translateY(-2px)';
      });
      
      input.addEventListener('blur', () => {
        input.parentElement.style.transform = 'translateY(0)';
      });
    });
  </script>
</body>
</html>
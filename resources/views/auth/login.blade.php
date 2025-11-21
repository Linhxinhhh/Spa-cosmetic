<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập - Lyn & Spa</title>

    <!-- Fonts và icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Inter', sans-serif; overflow-x: hidden; background: #F3F4F6; } /* Nền sáng xám nhạt */

.login-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #1E40AF, #1D4ED8); /* Gradient xanh đậm */
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    position: relative;
}
@keyframes gradientShift { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
.particle { position: absolute; background: rgba(255,255,255,0.5); border-radius: 50%; pointer-events: none; animation: float 6s ease-in-out infinite; }
.particle:nth-child(odd) { animation-delay: -2s; animation-direction: reverse; }
@keyframes float { 0%,100%{transform: translateY(0) rotate(0deg); opacity: 0.7} 50%{transform: translateY(-30px) rotate(180deg); opacity: 1} }

.glass-card { 
    background: rgba(255,255,255,0.8); /* Nền kính mờ sáng */
    backdrop-filter: blur(20px); 
    border: 1px solid rgba(30,64,175,0.3); /* Viền xanh đậm */
    border-radius: 24px; 
    box-shadow: 0 25px 50px rgba(0,0,0,0.05); 
    position: relative; 
    overflow: hidden; 
}
.glass-card::before { 
    content: ''; 
    position: absolute; 
    top: 0; 
    left: 0; 
    right: 0; 
    height: 1px; 
    background: linear-gradient(90deg, transparent, rgba(30,64,175,0.4), transparent); /* Hiệu ứng viền xanh đậm */
}
.logo-container { position: relative; margin-bottom: 2rem; }
.logo { 
    width: 80px; 
    height: 80px; 
    margin: 0 auto 1rem; 
    background: white; /* Gradient xanh đậm */
    border-radius: 20px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 2rem; 
    font-weight: 800; 
    color: white; 
    position: relative; 
    
 
}
@keyframes logoGlow { 0%{box-shadow: 0 15px 35px rgba(30,64,175,0.3)} 100%{box-shadow: 0 20px 40px rgba(30,64,175,0.4)} }
.logo::after { 
    content: ''; 
    position: absolute; 
    inset: -2px; 
    background: linear-gradient(135deg, #1E40AF, #1E3A8A, #1D4ED8); 
    border-radius: 22px; 
    z-index: -1; 
    opacity: 0.7; 
    filter: blur(8px); 
}

.input-group { position: relative; margin-bottom: 1.5rem; }
.input-field { 
    width: 100%; 
    padding: 16px 20px 16px 50px; 
    background: rgba(255,255,255,0.9); /* Nền input sáng */
    border: 2px solid rgba(30,64,175,0.2); /* Viền xanh đậm nhạt */
    border-radius: 16px; 
    color: #1F2937; /* Chữ tối */
    font-size: 16px; 
    outline: none; 
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1); 
    backdrop-filter: blur(10px); 
}
.input-field::placeholder { color: rgba(107,114,128,0.6); } /* Placeholder xám */
.input-field:focus { 
    border-color: #1E40AF; /* Xanh đậm khi focus */
    background: rgba(255,255,255,1); 
    transform: translateY(-2px); 
    box-shadow: 0 10px 25px rgba(30,64,175,0.2); 
}
.input-icon { 
    position: absolute; 
    left: 18px; 
    top: 50%; 
    transform: translateY(-50%); 
    color: rgba(107,114,128,0.7); /* Icon xám */
    font-size: 16px; 
    z-index: 2; 
    transition: color 0.3s ease; 
}
.input-field:focus + .input-icon { color: #1E40AF; } /* Icon xanh đậm khi focus */

.btn-primary { 
    width: 100%; 
    padding: 16px; 
    background: linear-gradient(135deg, #1E40AF, #1D4ED8); /* Gradient xanh đậm */
    border: none; 
    border-radius: 16px; 
    color: white; 
    font-size: 16px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1); 
    position: relative; 
    overflow: hidden; 
    display: flex; 
    align-items: center; 
    justify-content: center; /* Căn giữa nội dung button */
}
.btn-primary::before { 
    content: ''; 
    position: absolute; 
    top: 0; 
    left: -100%; 
    width: 100%; 
    height: 100%; 
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); 
    transition: left 0.5s; 
}
.btn-primary:hover::before { left: 100%; }
.btn-primary:hover { 
    transform: translateY(-3px); 
    box-shadow: 0 20px 40px rgba(30,64,175,0.4); 
    background: linear-gradient(135deg, #1E3A8A, #1E40AF); /* Gradient hover đậm hơn */
}
.btn-primary:active { transform: translateY(-1px); }

.checkbox-group { display: flex; align-items: center; justify-content: space-between; margin: 1.5rem 0; }
.custom-checkbox { display: flex; align-items: center; cursor: pointer; }
.custom-checkbox input[type="checkbox"] { width: 20px; height: 20px; margin-right: 12px; accent-color: #1E40AF; } /* Checkbox xanh đậm */
.custom-checkbox label { color: #1F2937; font-size: 14px; cursor: pointer; } /* Label chữ tối */
.forgot-password { color: #1E40AF; text-decoration: none; font-size: 14px; transition: color 0.3s ease; } /* Link xanh đậm */
.forgot-password:hover { color: #1E3A8A; } /* Hover đậm hơn */

.divider { display: flex; align-items: center; margin: 2rem 0; color: rgba(107,114,128,0.6); font-size: 14px; } /* Divider xám */
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(107,114,128,0.2); }
.divider span { padding: 0 1rem; }
.register-link { text-align: center; color: #1F2937; } /* Chữ tối */
.register-link a { color: #1E40AF; text-decoration: none; font-weight: 600; transition: color 0.3s ease; }
.register-link a:hover { color: #1E3A8A; }

.content-side { 
    display: flex; 
    flex-direction: column; 
    justify-content: center; 
    align-items: center; 
    color: white;/* Chữ tối */
    text-align: center; 
    padding: 2rem; 
    position: relative; 
}
.content-title { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; line-height: 1.2; }
.content-subtitle { font-size: 1.2rem; opacity: 0.9; margin-bottom: 2rem; max-width: 400px; }
.feature-list { display: flex; flex-direction: column; gap: 1rem; margin-top: 2rem; }
.feature-item { display: flex; align-items: center; gap: 1rem; opacity: 0.9; }
.feature-icon { 
    width: 40px; 
    height: 40px; 
    background: rgba(30,64,175,0.1); /* Nền icon xanh đậm nhạt */
    border-radius: 12px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    backdrop-filter: blur(10px); 
}
@media (max-width: 1024px) { .content-side { display: none; } }
@media (max-width: 768px) { 
    .glass-card { margin: 1rem; padding: 2rem 1.5rem; } 
    .content-title { font-size: 2rem; } 
}
.title { font-size: 2.5rem; font-weight: 800; color: #1F2937; margin-bottom: 0.5rem; text-align: center; } /* Tiêu đề chữ tối */
.subtitle { color: rgba(107,114,128,0.8); font-size: 1rem; text-align: center; margin-bottom: 2rem; } /* Phụ đề xám */
.footer { 
    position: fixed; 
    bottom: 0; 
    left: 0; 
    right: 0; 
    padding: 1rem; 
    text-align: center; 
    color: white; /* Footer xám */
    backdrop-filter: blur(10px); 
}
  </style>
</head>
<body class="login-container">
    <!-- Floating particles -->
    <div class="particle" style="width:20px;height:20px;top:20%;left:10%;"></div>
    <div class="particle" style="width:15px;height:15px;top:60%;left:80%;animation-delay:-1s;"></div>
    <div class="particle" style="width:25px;height:25px;top:30%;right:20%;animation-delay:-3s;"></div>
    <div class="particle" style="width:18px;height:18px;bottom:30%;left:70%;animation-delay:-4s;"></div>
    <div class="particle" style="width:12px;height:12px;top:80%;left:30%;animation-delay:-2s;"></div>

    <div style="min-height:100vh; display:flex;">
        <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:2rem;">
            <div class="glass-card" style="width:100%; max-width:400px; padding:3rem;">
                <div class="logo-container">
                    <div class="logo">
    <img style="width:100%;height:100%; border-radius: 15%;" src="{{asset('dashboard/images/logos/logoreplace.png')}}" alt="Logo" class="logo-image">
</div>
                    <h1 class="title">Lyn & Spa</h1>
                    <p class="subtitle">Chào mừng bạn trở lại</p>
                </div>
                 @php
                    $isAuthenticated = session('jwt_token') && auth('api')->check();
                @endphp
                   @if ($isAuthenticated)
                    <div class="text-center">
                        <p class="subtitle">Chào mừng, {{ auth('api')->user()->name }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-out-alt" style="margin-right:0.5rem;"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                @else
                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf
                    <div class="input-group">
                        <input type="email" name="email" value="{{ old('email') }}" class="input-field" placeholder="Địa chỉ email" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>

                    <div class="input-group">
                        <input type="password" name="password" class="input-field" placeholder="Mật khẩu" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>

                    <div class="checkbox-group">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Nhớ mật khẩu</label>
                        </div>
                        <a href="#" class="forgot-password">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt" style="margin-right:0.5rem;"></i>
                        Đăng nhập
                    </button>
                </form>

                <div class="divider"><span>hoặc</span></div>
                <div class="register-link">
                    Chưa có tài khoản? <a href="{{route('admin.register')}}">Đăng ký ngay</a>
                </div>
                  @endif
            </div>
        </div>

        <div style="flex:1;" class="content-side">
            <div>
                <h2 class="content-title">Trải nghiệm dịch vụ <span >đẳng cấp</span></h2>
                <p class="content-subtitle">Hệ thống quản lý hiện đại giúp bạn theo dõi và cập nhật dịch vụ một cách dễ dàng nhất.</p>
                <div class="feature-list">
                    <div class="feature-item"><div class="feature-icon"><i class="fas fa-shield-alt"></i></div><span>Bảo mật cao cấp</span></div>
                    <div class="feature-item"><div class="feature-icon"><i class="fas fa-clock"></i></div><span>Truy cập 24/7</span></div>
                    <div class="feature-item"><div class="feature-icon"><i class="fas fa-mobile-alt"></i></div><span>Tương thích mọi thiết bị</span></div>
                    <div class="feature-item"><div class="feature-icon"><i class="fas fa-spa"></i></div><span>Quản lý spa chuyên nghiệp</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
            <span>© 2024</span><span style="font-weight:600;">Lyn Cosmetic & Spa</span><span>•</span><span>Được thiết kế với</span><i class="fas fa-heart" style="color:#f87171;"></i>
        </div>
    </div>

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            background: 'rgba(72, 187, 120, 0.9)',
            color: 'white',
            iconColor: 'white'
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            background: 'rgba(248, 113, 113, 0.9)',
            color: 'white',
            iconColor: 'white'
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Đăng nhập thất bại',
            html: `@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach`,
            timer: 4000,
            showConfirmButton: true,
            position: 'center',
            background: 'rgba(255, 0, 0)',
            color: 'white',
            iconColor: 'white'
        });
    </script>
    @endif

    <script>
        @if (session('jwt_token'))
            localStorage.setItem('jwt_token', '{{ session('jwt_token') }}');
        @endif
        document.querySelector('form[action="{{ route('admin.logout') }}"]')?.addEventListener('submit', function() {
            localStorage.removeItem('jwt_token');
        });
    </script>
</body>
</html>

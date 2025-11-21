
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Xác minh email</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root { --bg:#f6f7fb; --card:#fff; --text:#1f2937; --muted:#6b7280; --primary:#2563eb; --border:#e5e7eb; --success:#10b981; }
    * { box-sizing: border-box; }
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji','Segoe UI Emoji'; background: var(--bg); color: var(--text);}
    .container { max-width: 860px; margin: 40px auto; padding: 0 16px;}
    .card { background: var(--card); border:1px solid var(--border); border-radius: 14px; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);}
    h1 { margin: 0 0 8px; font-size: 24px; }
    p  { margin: 6px 0; color: var(--muted); line-height: 1.6;}
    .row { display:flex; flex-wrap:wrap; gap: 12px; margin-top: 18px;}
    .btn { appearance:none; border:1px solid var(--border); background:#fff; color:var(--text); padding:10px 16px; border-radius:10px; cursor:pointer; font-weight:600;}
    .btn-primary { background: var(--primary); color:#fff; border-color: transparent; }
    .btn-outline  { background:#fff; }
    .btn:disabled { opacity:.6; cursor:not-allowed;}
    .alert { margin: 14px 0 0; padding: 12px 14px; border-radius: 10px; background: #ecfdf5; color:#065f46; border:1px solid #a7f3d0;}
    .meta { font-size: 14px; color: var(--muted);}
    .email { font-weight: 600; color: var(--text);}
    .divider { height:1px; background:var(--border); margin: 18px 0; }
    @media (max-width: 520px) { .row { flex-direction: column; } }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Vui lòng xác minh email của bạn</h1>
      <p>
        Xin chào <span class="email">{{ auth()->user()->name ?? 'bạn' }}</span>!
        Chúng mình đã gửi một liên kết xác minh tới địa chỉ
        <span class="email">{{ auth()->user()->email ?? '' }}</span>.
      </p>
      <p>Nhấp vào liên kết trong email để hoàn tất kích hoạt tài khoản. Nếu chưa thấy, hãy kiểm tra hộp <b>Spam/Quảng cáo</b>.</p>

      @if (session('status'))
        <div class="alert">
          {{ session('status') }}
        </div>
      @endif

      <div class="divider"></div>

      <form class="row" method="POST" action="{{ route('users.verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary">
          Gửi lại email xác minh
        </button>

        {{-- về trang chủ --}}
        <a class="btn btn-outline" href="{{ route('users.home') }}">Về trang chủ</a>

        {{-- đăng xuất --}}
        <form method="POST" action="{{ route('users.logout') }}" style="display:inline;">
          @csrf
          <button type="submit" class="btn btn-outline">Đăng xuất</button>
        </form>
      </form>

      <p class="meta">Lưu ý: Vì lý do bảo mật, nút gửi lại có giới hạn tần suất.</p>
    </div>
  </div>
</body>
</html>

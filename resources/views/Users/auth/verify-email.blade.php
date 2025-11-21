<!doctype html><html><body>
  <h1>Vui lòng xác minh email</h1>
  @if (session('status')) <div>{{ session('status') }}</div> @endif
  <form method="POST" action="{{ route('verification.send') }}"> @csrf
    <button type="submit">Gửi lại email xác minh</button>
  </form>
  <a href="{{ route('users.login') }}">Quay lại đăng nhập</a>
</body></html>

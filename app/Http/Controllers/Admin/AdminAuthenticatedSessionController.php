<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class AdminAuthenticatedSessionController extends Controller
{
        /**
     * Hiển thị form đăng ký admin hoặc staff.
     */
   public function registerForm(): View
{
    $roles = Role::whereIn('name', ['admin','staff'])
        ->orderBy('name')
        ->get(['role_id','name']);
    return view('auth.register', compact('roles'));
}

public function register(Request $request): RedirectResponse
{
    $data = $request->validate([
        'name'     => ['required','string','max:255'],
        'email'    => ['required','email','max:255','unique:users,email'],
        'phone'    => ['nullable','string','max:20'],
        'password' => ['required','confirmed', Rules\Password::defaults()],
        'role_id'  => ['required','integer','exists:roles,role_id'], // <── dùng role_id
    ]);

    $user = User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'phone'    => $data['phone'] ?? null,
        'password' => Hash::make($data['password']),
    ]);

    $user->roles()->sync([$data['role_id']]); // gán role vào bảng role_user

    Auth::guard('admin')->login($user);

return redirect()
    ->intended(\App\Providers\RouteServiceProvider::ADMIN_HOME)
    ->with('status', 'Đăng ký thành công! Chào mừng bạn.');

}
    /**
     * Hiển thị form đăng nhập admin.
     */
    public function create(): View
    {
        // Có thể dùng lại view auth.login của Breeze nếu form action trỏ đến route admin
        return view('auth.login'); // hoặc 'admin.auth.login' nếu bạn tách riêng file
    }

    /**
     * Xử lý đăng nhập admin (guard: admin).
     */
    public function store(Request $request): RedirectResponse
    {
        $cred = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($cred, $remember)) {
            $request->session()->regenerate();

            // (Tuỳ chọn) chặn user không có quyền vào admin
            $user = Auth::guard('admin')->user();
            $ok = method_exists($user, 'hasAnyRole')
                ? $user->hasAnyRole(['admin','staff'])
                : in_array($user->role ?? null, ['admin','staff'], true);

            if (! $ok) {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Bạn không có quyền vào trang quản trị.'])->onlyInput('email');
            }

            return redirect()->intended(\App\Providers\RouteServiceProvider::ADMIN_HOME)
                   ->with('success','Đăng nhập thành công! Chào mừng bạn trở lại');
        }

        return back()
            ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])
            ->onlyInput('email');
    }

    /**
     * Đăng xuất admin (guard: admin).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success','Đăng xuất thành công!');
    }
}

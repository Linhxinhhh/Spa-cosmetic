<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\DB;

class UserAuthController extends Controller
{
    public function showLogin()    { return view('Users.auth.login'); }
    public function showRegister() { return view('Users.auth.register'); }
    public function showForgot()   { return view('Users.auth.forgot-password'); }
    public function showReset($token) { return view('Users.auth.reset-password', compact('token')); }

public function register(Request $request)
{
    $data = $request->validate([
        'name'     => ['required','string','max:100'],
        'email'    => ['required','email','max:255','unique:users,email'],
        'password' => [
            'required','confirmed',
            Password::min(8)->letters()->numbers()->mixedCase()->symbols()->uncompromised(),
        ],
    ]);

    $user = User::create([
        'name'     => $data['name'],
        'email'    => strtolower(trim($data['email'])),
        'password' => Hash::make($data['password']),
    ]);

    event(new Registered($user)); // gửi mail verify
    Auth::login($user);           // vẫn giữ đăng nhập để xem trang notice (có middleware auth)

    if (method_exists($user, 'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
        // Auth::guard('web')->logout();        // ❌ BỎ DÒNG NÀY
        return redirect()->route('users.verification.notice'); // ✅ ĐÚNG
    }

    // return redirect()->intended(route('users.account')); // ❌
    return redirect()->intended(RouteServiceProvider::HOME);
                 // ✅ CHỈNH Ở ĐÂY
}
private function mergeGuestCartToDatabase(int $userId): void
{
    $sessionItems = session('cart.items', []); // ['product_id' => ['qty' => N]]
    if (empty($sessionItems)) return;

    // nạp product 1 lượt
    $productIds = array_keys($sessionItems);
    $products = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

    DB::transaction(function () use ($userId, $sessionItems, $products) {
        // lấy/ tạo giỏ đang active cho user
        $cart = Cart::getActiveCart($userId); // hàm sẵn có của bạn
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $userId,
                'status'  => 'active',
            ]);
        }

        // nạp sẵn các dòng CartItem hiện có để cộng dồn
        $existing = $cart->items()->get()->keyBy('product_id');

        foreach ($sessionItems as $pid => $row) {
            $pid = (int)$pid;
            $qty = max(1, (int)($row['qty'] ?? 1));

            // bỏ qua sản phẩm không còn tồn tại/không active nếu cần
            $product = $products->get($pid);
            if (!$product) continue;

            // (tuỳ bạn) ràng buộc tồn kho tại đây
            // $qty = min($qty, max(0, (int)$product->stock));

            if ($existing->has($pid)) {
                // cộng dồn số lượng
                $item = $existing->get($pid);
                $item->update([
                    'quantity' => (int)$item->quantity + $qty,
                ]);
            } else {
                // chụp giá tại thời điểm merge (nếu bạn có cột price_snapshot)
                $price = function_exists('product_final_price')
                       ? (int) product_final_price($product)
                       : (int) ($product->price ?? 0);

                CartItem::create([
                    'cart_id'    => $cart->cart_id,   // đúng theo schema của bạn
                    'product_id' => $pid,
                    'quantity'   => $qty,
                    // 'price'    => $price,          // nếu bảng có cột giá chụp
                ]);
            }
        }
    });

    // xoá session của giỏ khách sau khi merge thành công
    session()->forget('cart.items');
}

public function login(Request $request)
{
    $cred = $request->validate([
        'email'    => ['required','email'],
        'password' => ['required'],
    ]);

    $credentials = [
        'email'    => strtolower(trim($cred['email'])),
        'password' => $cred['password'],
    ];

    if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        $user = Auth::user();
        if (method_exists($user,'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
            // Auth::logout();                                   // ❌ BỎ DÒNG NÀY
            return redirect()->route('users.verification.notice'); // ✅ ĐÚNG
        }
         $this->mergeGuestCartToDatabase(Auth::id());

        return redirect()->intended(RouteServiceProvider::HOME);
// ✅
    }

    return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->onlyInput('email');
}


    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('users.login');
    }

    // Gửi mail reset
    public function sendReset(Request $request)
    {
        $request->validate(['email' => ['required','email']]);

        $status = PasswordBroker::sendResetLink([
            'email' => strtolower(trim($request->input('email'))),
        ]);

        return $status === PasswordBroker::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // Đổi mật khẩu qua token
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => [
                'required','confirmed',
                Password::min(8)->letters()->numbers()->mixedCase()->symbols()->uncompromised(),
            ],
        ]);

        $status = PasswordBroker::reset(
            [
                'email' => strtolower(trim($request->input('email'))),
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $request->input('token'),
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route('users.login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}

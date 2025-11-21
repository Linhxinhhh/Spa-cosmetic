<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Models\Cart;
use App\Models\Product;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
  public function boot(): void
    {
        View::composer('*', function ($view) {
            $cart       = null;   // giỏ DB (khi đã đăng nhập)
            $cartCount  = 0;      // tổng số lượng
            $cartTotal  = 0;      // tổng tiền

            if (Auth::check()) {
                // === USER: đọc giỏ từ DB ===
                $cart = Cart::getActiveCart(Auth::id())->load('items.product');

                if ($cart) {
                    $cartCount = (int) $cart->items->sum('quantity');
                    $cartTotal = $cart->items->sum(function ($item) {
                        // product_final_price(Product $p) -> helper của bạn
                        return product_final_price($item->product) * (int)$item->quantity;
                    });
                }
            } else {
                // === GUEST: đọc giỏ từ SESSION ===
                // HỖ TRỢ NHIỀU KIỂU LƯU:
                // 1) 'cart.items' => [ product_id => ['qty'=>N, 'price'?(optional)] ]
                // 2) 'cart'       => [ ['product_id'=>..., 'quantity'=>..., 'price'?(optional)] , ... ]
                $sessionItems = session('cart.items');
                if (!is_array($sessionItems) || empty($sessionItems)) {
                    $sessionItems = session('cart', []);
                }

                // Chuẩn hóa về dạng: [['product_id'=>X, 'qty'=>Y, 'price'?(optional)], ...]
                $normalized = [];

                if (!empty($sessionItems)) {
                    // Trường hợp 1: key là product_id
                    //   $sessionItems = [ 12 => ['qty'=>2], 34 => ['qty'=>1] ]
                    if (array_keys($sessionItems) !== range(0, count($sessionItems) - 1)) {
                        foreach ($sessionItems as $pid => $row) {
                            $normalized[] = [
                                'product_id' => (int)$pid,
                                'qty'        => (int)($row['qty'] ?? $row['quantity'] ?? 1),
                                'price'      => isset($row['price']) ? (int)$row['price'] : null,
                            ];
                        }
                    } else {
                        // Trường hợp 2: mảng tuần tự các item
                        //   $sessionItems = [ ['product_id'=>12,'quantity'=>2], ... ]
                        foreach ($sessionItems as $row) {
                            $normalized[] = [
                                'product_id' => (int)($row['product_id'] ?? 0),
                                'qty'        => (int)($row['qty'] ?? $row['quantity'] ?? 1),
                                'price'      => isset($row['price']) ? (int)$row['price'] : null,
                            ];
                        }
                    }
                }

                // Tính count
                $cartCount = collect($normalized)->sum('qty');

                // Load product để lấy giá hiện tại (nếu không có 'price' trong session)
                $productIds = collect($normalized)->pluck('product_id')->filter()->unique()->all();
                $products   = empty($productIds)
                    ? collect()
                    : Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

                // Tính total
                $cartTotal = collect($normalized)->sum(function ($row) use ($products) {
                    $qty = (int)($row['qty'] ?? 1);

                    // Ưu tiên dùng helper product_final_price nếu tìm được Product
                    $pid = (int)($row['product_id'] ?? 0);
                    $p   = $products->get($pid);

                    if ($p) {
                        $price = (int) product_final_price($p);
                    } else {
                        // fallback: dùng price trong session nếu có, nếu không thì 0
                        $price = (int)($row['price'] ?? 0);
                    }

                    return $price * $qty;
                });
            }
              // đồng bộ timezone cho session MySQL
    if (config('database.default') === 'mysql') {
        DB::statement("SET time_zone = '+07:00'");
    }

    // (tuỳ chọn) đảm bảo PHP timezone theo config
    date_default_timezone_set(config('app.timezone', 'Asia/Ho_Chi_Minh'));
                if (config('database.connections.mysql.timezone')) {
                DB::statement("SET time_zone = '".config('database.connections.mysql.timezone')."'");
    }

            // chia sẻ cho tất cả view (header dùng $cartCount, $cartTotal)
            $view->with([
                'cart'      => $cart,
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
            ]);
        });
    }

    
}

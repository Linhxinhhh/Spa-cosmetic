<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductCategory;
use App\Models\ServiceCategory;
use App\Models\Product;
use App\Models\Cart;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Chỉ áp cho layout/user để tránh composer khác ghi đè
        View::composer(['Users.*','users.*'], function ($view) {
            $headerCartCount = 0;
            $headerCartTotal = 0;

            // --- menu ---
            $productParents = ProductCategory::query()
                ->select(['category_id','category_name','slug'])
                ->whereNull('parent_id')->where('status',1)
                ->with(['children' => fn($q)=>$q->select(['category_id','category_name','slug','parent_id'])
                    ->where('status',1)->orderBy('category_name')])
                ->orderBy('category_name')->get();

            $serviceParents = ServiceCategory::query()
                ->select(['category_id','category_name','slug'])
                ->whereNull('parent_id')->where('status',1)
                ->with(['children' => fn($q)=>$q->select(['category_id','category_name','slug','parent_id'])
                    ->where('status',1)->orderBy('category_name')])
                ->orderBy('category_name')->get();

            // migrate session cũ -> cart.items
            if (!session()->has('cart.items') && session()->has('cart')) {
                $old = (array) session('cart', []);
                $items = [];
                foreach ($old as $pid => $row) {
                    $items[(string)$pid] = ['qty' => max(1, (int)($row['qty'] ?? 1))];
                }
                session(['cart.items' => $items]);
                session()->forget('cart');
            }

            // Chuẩn hoá giá: luôn ra số VND (không nhân 1000 ở đây)
            $normalize = function ($val): int {
                if (is_numeric($val)) return (int) round($val);
                if (is_string($val)) return (int) preg_replace('/\D+/', '', $val);
                return 0;
            };
            $priceOf = function ($p) use ($normalize): int {
                $raw = function_exists('product_final_price')
                    ? product_final_price($p)
                    : ($p->price ?? 0);
                return $normalize($raw);
            };

            if (Auth::check()) {
                $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
                $items = $cart->items()->with('product')->get();
                $headerCartCount = (int) $items->sum('quantity');
                $headerCartTotal = (int) $items->sum(fn($i) => $priceOf($i->product) * (int)$i->quantity);
            } else {
                $sessionItems = (array) session('cart.items', []);
                if ($sessionItems) {
                    $ids = array_map('intval', array_keys($sessionItems));
                    $products = Product::whereIn('product_id', $ids)->get()->keyBy('product_id');
                    foreach ($sessionItems as $pid => $row) {
                        if ($p = $products->get((int)$pid)) {
                            $qty = max(1, (int)($row['qty'] ?? 1));
                            $headerCartCount += $qty;
                            $headerCartTotal += $priceOf($p) * $qty;
                        }
                    }
                }
            }

            // Nếu muốn KHÔNG cộng VAT ở header: bỏ 3 dòng dưới
            $vatRate = 0.05;
            $headerCartTotal = (int) round($headerCartTotal + $headerCartTotal * $vatRate);

            $view->with([
                'productParents'   => $productParents,
                'serviceParents'   => $serviceParents,
                'headerCartCount'  => (int) $headerCartCount,
                'headerCartTotal'  => (int) $headerCartTotal,
            ]);
        });
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    /** 
     * BỎ middleware auth để KHÁCH vẫn dùng được session-cart. 
     * (Checkout/Pay hãy bắt buộc auth ở controller/route khác)
     */
    public function __construct()
    {
        // $this->middleware('auth'); // ❌ bỏ đi
    }

    /** Lấy giỏ DB hiện tại của user (tạo nếu chưa có) */
    private function myCart(): ?Cart
    {
        if (!Auth::check()) return null;
        return Cart::getActiveCart(Auth::id()); // hàm của bạn
    }

    /** Helpers cho SESSION-CART (khách) */
    private function getSessionItems(): array
    {
        // dạng: ['product_id' => ['qty'=>N]]
        return session('cart.items', []);
    }

    private function putSessionItems(array $items): void
    {
        session(['cart.items' => $items]);
    }

    /** Chuẩn hóa dữ liệu items cho view: 
     *  Trả về Collection gồm các item có: product (Model Product) & quantity (int)
     */
    private function buildViewItemsForGuest(array $sessionItems)
    {
        $productIds = array_keys($sessionItems);
        $products   = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

        return collect($sessionItems)->map(function ($row, $pid) use ($products) {
            $p = $products->get((int)$pid);
            if (!$p) return null;
            return (object)[
                'product'  => $p,
                'quantity' => max(1, (int)($row['qty'] ?? 1)),
            ];
        })->filter();
    }

    /** GET /cart */
public function index()
{
    $orderCode = 'LCS' . now()->format('YmdHis');

    // ---- USER ĐANG ĐĂNG NHẬP ----
    if (Auth::check()) {
        $cart  = $this->myCart();
        $items = $cart->items()->with(['product.imagesRel'])->get();

        // TÍNH TỔNG TIỀN
        $subtotal = $items->sum(function ($it) {
            return product_final_price($it->product) * $it->quantity;
        });

        return view('Users.cart.index', [
            'cart'      => $cart,
            'items'     => $items,
            'orderCode' => $orderCode,
            'subtotal'  => $subtotal,
        ]);
    }

    // ---- GUEST ----
    $sessionItems = $this->getSessionItems();
    $items        = $this->buildViewItemsForGuest($sessionItems);
    $cart         = null;

    // TÍNH TỔNG TIỀN
    $subtotal = $items->sum(function ($it) {
        return product_final_price($it->product) * $it->quantity;
    });

    return view('Users.cart.index', [
        'cart'      => $cart,
        'items'     => $items,
        'orderCode' => $orderCode,
        'subtotal'  => $subtotal,
    ]);
}


public function add(Request $request, Product $product)
{
    $qty = max(1, (int) $request->input('quantity', 1));

    if (auth()->check()) {
        $cart = $this->myCart();
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => auth()->id(),
                'status'  => 'active',
            ]);
        }

        $item = $cart->items()->where('product_id', $product->product_id)->first();
        if ($item) {
            $item->increment('quantity', $qty);
        } else {
            $cart->items()->create([
                'product_id' => $product->product_id,
                'quantity'   => $qty,
                'price'      => product_final_price($product),
            ]);
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ.');
    }

    // KHÁCH (session)
    $items = session('cart.items', []);   // ['product_id' => ['qty'=>N]]
    $pid   = (string) $product->product_id;

    if (isset($items[$pid])) {
        $items[$pid]['qty'] += $qty;
    } else {
        $items[$pid] = ['qty' => $qty];
    }

    session(['cart.items' => $items]);

    return back()->with('success', 'Đã thêm sản phẩm vào giỏ.');
}



    /** PATCH /cart/{item} 
     *  - USER: {item} là CartItem model (route-model-binding)
     *  - GUEST: {item} sẽ là product_id (sửa route: users.cart.update với {item} là id)
     */
public function update(Request $request, $item)
{
    $data = $request->validate([
        'quantity' => ['required','integer','min:1','max:999'],
    ]);
    $qty = (int)$data['quantity'];

    if (auth()->check()) {
        $cart = $this->myCart();
        $row  = CartItem::where('item_id', $item)->firstOrFail();

        if ($row->cart_id !== $cart->cart_id) abort(403);

        $row->update(['quantity' => $qty]);
        return back()->with('success', 'Cập nhật giỏ hàng thành công.');
    }

    // Guest
    $items = $this->getSessionItems();
    $pid   = (string)$item;
    if (isset($items[$pid])) {
        $items[$pid]['qty'] = $qty;
        $this->putSessionItems($items);
    }

    return back()->with('success', 'Cập nhật giỏ hàng (tạm) thành công.');
}

public function remove($item)
{
    if (auth()->check()) {
        $cart = $this->myCart();
        $row  = CartItem::where('item_id', $item)->firstOrFail();

        if ($row->cart_id !== $cart->cart_id) abort(403);

        $row->delete();
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ.');
    }

    // Guest
    $items = $this->getSessionItems();
    $pid   = (string)$item;
    if (isset($items[$pid])) {
        unset($items[$pid]);
        $this->putSessionItems($items);
    }

    return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ (tạm).');
}

}
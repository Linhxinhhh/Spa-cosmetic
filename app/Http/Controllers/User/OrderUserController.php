<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderUserController extends Controller
{
    // Danh sách đơn hàng
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('order_id', 'DESC')
            ->paginate(10);

        return view('Users.orders.index', compact('orders'));
    }

    // Chi tiết đơn hàng
 // Chi tiết đơn hàng
    public function show($orderId)
    {
        $order = Order::with('items.product')
            ->where('order_id', $orderId)
            ->where('user_id', Auth::id()) // chặn xem đơn của người khác
            ->firstOrFail();

        // -----------------------
        // 🧮 TÍNH TẠM TÍNH (subtotal)
        // -----------------------
        $itemsTotal = $order->items->sum(function ($it) {
            $base = (int)$it->price * (int)$it->quantity;

            // xử lý chiết khấu
            $disc = 0;
            if (!is_null($it->discount_price) && $it->discount_price > 0) {
                $disc = (int)$it->discount_price;
            } elseif ($it->discount_percent > 0) {
                $disc = (int) round($base * $it->discount_percent / 100);
            }

            return max(0, $base - $disc);
        });

        // -----------------------
        // 🧮 VAT 5%
        // -----------------------
        $vatAmount = (int) round($itemsTotal * 0.05);

        // -----------------------
        // 🧮 Tổng thanh toán
        // -----------------------
        $grandTotal = $itemsTotal + $vatAmount;

        return view('Users.orders.show', [
            'order'       => $order,
            'itemsTotal'  => $itemsTotal,
            'vatAmount'   => $vatAmount,
            'grandTotal'  => $grandTotal,
        ]);
    }
}

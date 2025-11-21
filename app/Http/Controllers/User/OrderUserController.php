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
    public function show($orderId)
    {
        $order = Order::with('items.product')
            ->where('order_id', $orderId)
            ->where('user_id', Auth::id()) // chặn xem đơn của người khác
            ->firstOrFail();

        return view('Users.orders.show', compact('order'));
    }
}

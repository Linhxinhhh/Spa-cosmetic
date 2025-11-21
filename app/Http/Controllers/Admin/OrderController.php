<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
public function index(Request $r)
{
    $filters = [
        'q'              => $r->q,
        'status'         => $r->status,
        'payment_status' => $r->payment_status,
        'date_from'      => $r->date_from,
        'date_to'        => $r->date_to,
    ];

    $orders = Order::query()
        ->select('orders.*')
        // Tính VAT 5% và tổng sau VAT ngay trong SQL
        ->selectRaw('ROUND(COALESCE(total_amount,0) * 0.05, 2)  as vat_amount')
        ->selectRaw('ROUND(COALESCE(total_amount,0) * 1.05, 2) as total_with_vat')
        ->with([
            'user:user_id,name,email,phone',
            'user.customer:user_id,address,birthday,loyalty_points',
        ])
        ->withCount('items')
        ->filter($filters)
        ->latest('created_at')
        ->paginate(20)
        ->withQueryString();

    return view('dashboard.orders.index', [
        'orders'    => $orders,
        'filters'   => $filters,
        'statusMap' => Order::STATUS,
        'payMap'    => Order::PAYMENT_STATUS,
    ]);
}

public function show(Order $order)
{
    // nếu cần: load trước quan hệ để dùng thumbnail/tên dịch vụ
    $order->load([
        'items.product.mainImageRel',   // ảnh chính (is_main = 1)
        'items.product.imagesRel',      // fallback ảnh đầu tiên
        'items.service',                // nếu item là dịch vụ
    ]);

    // Tạm tính từ các dòng hàng
    $itemsTotal = $order->items->sum(function ($it) {
        $base = (int)$it->price * (int)$it->quantity;

        // ưu tiên discount_price, nếu null dùng discount_percent
        $disc = !is_null($it->discount_price) ? (int)$it->discount_price : 0;
        if ($disc === 0 && (float)$it->discount_percent > 0) {
            $disc = (int) round($base * ((float)$it->discount_percent) / 100);
        }
        return max(0, $base - $disc);
    });

    // VAT 5% (làm tròn theo VND)
    $vatAmount  = (int) round($itemsTotal * 0.05);
    // Tổng cộng = tạm tính + VAT (không cộng phí vận chuyển nữa)
    $grandTotal = $itemsTotal + $vatAmount;

    return view('dashboard.orders.show', [
        'order'       => $order,
        'statusMap'   => Order::STATUS,
        'payMap'      => Order::PAYMENT_STATUS,
        'itemsTotal'  => $itemsTotal,
        'vatAmount'   => $vatAmount,
        'grandTotal'  => $grandTotal,
    ]);
}



    public function updateStatus(Request $r, Order $order)
    {
        $r->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUS))],
        ]);
        $order->update(['status' => $r->status]);
        return back()->with('success','Cập nhật trạng thái đơn hàng thành công.');
    }

    public function updatePayment(Request $r, Order $order)
    {
        $r->validate([
            'payment_status' => ['required', Rule::in(array_keys(Order::PAYMENT_STATUS))],
        ]);
        $order->update(['payment_status' => $r->payment_status]);
        return back()->with('success','Cập nhật trạng thái thanh toán thành công.');
    }
}

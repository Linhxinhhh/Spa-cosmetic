<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Order, OrderItem, Payment};
use App\Services\{MomoService, VnpayService, CustomerSyncService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show()
    {
        $user  = auth()->user();
        $cart  = $user->cart()->with('items.product')->first();
        $items = $cart?->items ?? collect();
        $amount = $items->sum(fn($i) => (int) product_final_price($i->product) * (int) $i->quantity);

        // Mã đơn gợi ý (sẽ gửi kèm form). Đơn CHỈ tạo khi ấn thanh toán.
        $orderCode = 'LCS'.now()->format('YmdHis');

        return view('Users.checkout.show', compact('cart','amount','orderCode'));
    }

    public function pay(Request $request, VnpayService $vnpay, MomoService $momo)
    {
        $request->validate([
            'provider'   => 'required|in:vnpay,momo',
            'order_code' => 'required|string',
            // tuỳ ý: 'phone','address','note'...
        ]);

        $user = $request->user();
        $cart = $user->cart()->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error','Giỏ hàng trống.');
        }

        // Nếu trùng mã đơn, sinh lại để tránh unique conflict
        $orderCode = $request->order_code;
        if (Order::where('order_code',$orderCode)->exists()) {
            $orderCode = 'LCS'.now()->format('YmdHis').Str::upper(Str::random(3));
        }

        $txnRef = Str::upper(Str::random(12));

        // 1) TẠO ĐƠN + ITEM (PENDING)
        $order = DB::transaction(function () use ($user, $cart, $orderCode, $request) {
            $order = $user->orders()->create([
                'order_code'      => $orderCode,
                'total_amount'    => 0,                 // cập nhật sau
                'payment_method'  => $request->provider, // 'vnpay' | 'momo'
                'payment_status'  => 'pending',
                'status'          => 'pending',
                'shipping_address'=> $request->input('address'),
                'phone'           => $request->input('phone'),
                'note'            => $request->input('note'),
            ]);

            $total = 0;
            foreach ($cart->items as $ci) {
                $price = (int) product_final_price($ci->product);
                $qty   = (int) $ci->quantity;

                OrderItem::create([
                    'order_id'        => $order->order_id,
                    'product_id'      => $ci->product_id,
                    'quantity'        => $qty,
                    'price'           => $price,
                    'discount_percent'=> 0,
                    'discount_price'  => 0,
                ]);

                $total += $price * $qty;
            }

            $order->update(['total_amount' => $total]);
            return $order;
        });
         CustomerSyncService::touchFromOrder($order, false);

        $amount  = (int) $order->total_amount;
        $payload = [
            'order_code' => $order->order_code,
            'amount'     => $amount,
            'order_info' => $order->order_code,   // DÙNG CHÍNH MÃ ĐƠN để đối chiếu
            'txn_ref'    => $txnRef,
        ];

        // 2) TẠO PAYMENT gắn order_id
        Payment::create([
            'provider'        => $request->provider,
            'order_code'      => $order->order_code,
            'order_id'        => $order->order_id,
            'amount'          => $amount,
            'currency'        => 'VND',
            'status'          => 'pending',
            'txn_ref'         => $txnRef,
            'request_payload' => json_encode($payload),
        ]);

        // 3) ĐI CỔNG
        if ($request->provider === 'vnpay') {
            $url = $vnpay->createPaymentUrl($payload);  // trả về URL đầy đủ
            return redirect()->away($url);
        } else {
            $res = $momo->createPayment($payload);      // JSON từ MoMo
            if (($res['resultCode'] ?? 99) == 0 && !empty($res['payUrl'])) {
                return redirect()->away($res['payUrl']);
            }
            return back()->with('error','Không thể tạo thanh toán MoMo.');
        }
    }

    // VNPAY RETURN/IPN
    public function vnPayCheck(Request $request, VnpayService $vnpay)
    {
        $params = $request->query();

        if (!$vnpay->verifyChecksum($params)) {
            return $request->routeIs('vnpay.return')
                ? redirect()->route('checkout.show')->with('error','Chữ ký VNPAY không hợp lệ.')
                : response()->json(['RspCode'=>'97','Message'=>'Invalid Checksum'], 200);
        }

        // Ưu tiên match theo order_code (vnp_OrderInfo), fallback txn_ref
        $payment = Payment::where('order_code', $params['vnp_OrderInfo'] ?? '')
                    ->orWhere('txn_ref', $params['vnp_TxnRef'] ?? '')
                    ->latest()->first();

        $isOk = (($params['vnp_ResponseCode'] ?? null) === '00');

        if ($payment) {
            $payment->update([
                'status'           => $isOk ? 'success' : 'failed',
                'txn_ref'          => $params['vnp_TxnRef'] ?? $payment->txn_ref,
                'response_payload' => json_encode($params),
            ]);

            // Cập nhật ORDER
            if ($payment->order_id) {
                Order::where('order_id',$payment->order_id)->update([
                    'payment_status' => $isOk ? 'paid' : 'failed',
                    'status'         => $isOk ? 'processing' : 'pending', // hoặc 'cancelled' tuỳ business
                ]);
            }
        }

        if ($request->routeIs('vnpay.return')) {
            return redirect()->route('checkout.show')->with($isOk ? 'success' : 'error',
                $isOk ? 'Thanh toán VNPAY thành công.' : 'Thanh toán VNPAY thất bại.');
        }
        return response()->json(
            ['RspCode' => $isOk ? '00' : '02', 'Message' => $isOk ? 'Confirm Success' : 'Transaction Failed'],
            200
        );
    }

    // MoMo RETURN/IPN (đơn giản hoá)
    public function result(Request $request)
    {
        $res = $request->all();

        $payment = Payment::where('order_code', $res['orderId'] ?? '')
                    ->orWhere('txn_ref', $res['requestId'] ?? '')
                    ->latest()->first();

        $isOk = (($res['resultCode'] ?? null) == 0);

        if ($payment) {
            $payment->update([
                'status'           => $isOk ? 'success' : 'failed',
                'txn_ref'          => $res['requestId'] ?? $payment->txn_ref,
                'response_payload' => json_encode($res),
            ]);

            if ($payment->order_id) {
                Order::where('order_id',$payment->order_id)->update([
                    'payment_status' => $isOk ? 'paid' : 'failed',
                    'status'         => $isOk ? 'processing' : 'pending',
                ]);
            }
        }

        return redirect()->route('checkout.show')
            ->with($isOk ? 'success' : 'error', $isOk ? 'Thanh toán MoMo thành công.' : 'Thanh toán MoMo thất bại.');
    }
}

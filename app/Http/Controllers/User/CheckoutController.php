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
            'provider'   => 'required|in:vnpay,momo,cod',  
            'order_code' => 'required|string',
            'phone'      => 'required|string|max:20',
            'address'    => 'required|string|max:255',
            'note'       => 'nullable|string',
        ]);

        $user = $request->user();
        $user->update([
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);
        $cart = $user->cart()->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error','Giỏ hàng trống.');
        }
        $orderCode = $request->order_code;
        if (Order::where('order_code',$orderCode)->exists()) {
            $orderCode = 'LCS'.now()->format('YmdHis').Str::upper(Str::random(3));
        }

        $txnRef_requertId =  Str::uuid()->toString();
        $txnRef_orderId =  Str::uuid()->toString();

        // 1) TẠO ĐƠN + ITEM (PENDING)
        $total = 0;
        foreach ($cart->items as $ci) {
            $price = (int) product_final_price($ci->product);
            $qty   = (int) $ci->quantity;
            $total += $price * $qty;
        }

        $amount  = (int) $total;
        
        $payload = [
            'order_code' => $orderCode,
            'amount'     => $amount,
            'order_info' => $orderCode,   // DÙNG CHÍNH MÃ ĐƠN để đối chiếu
            'txn_ref'    => $txnRef_requertId,
            'txn_ref_tmp'    => $txnRef_orderId,
        ];
        if ($request->provider === 'vnpay') {
            session([
                'checkout_phone' => $request->phone,
                'checkout_address' => $request->address,
                'checkout_note' => $request->note,
                'checkout_provider'=>'vnpay',
                'checkout_order_code'=>$orderCode
            ]);
            $url = $vnpay->createPaymentUrl($payload);  // trả về URL đầy đủ
      
            return redirect()->away($url);
        } else if ($request->provider === 'momo'){
            session([
                'checkout_phone' => $request->phone,
                'checkout_address' => $request->address,
                'checkout_note' => $request->note,
                'checkout_provider'=>'momo',
                'checkout_order_code'=>$orderCode
            ]);
            $res = $momo->createPayment($payload);      // JSON từ MoMo
            if (($res['resultCode'] ?? 99) == 0 && !empty($res['payUrl'])) {
                return redirect()->away($res['payUrl']);
            }
            return back()->with('error','Không thể tạo thanh toán MoMo.');
        }
        else{
            session([
                'checkout_phone' => $request->phone,
                'checkout_address' => $request->address,
                'checkout_note' => $request->note,
                'checkout_provider'=>'cod',
                'checkout_order_code'=>$orderCode
            ]);
            return redirect()->away(url('/users/paysuccess'));
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

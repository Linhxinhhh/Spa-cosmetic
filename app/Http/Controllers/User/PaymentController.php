<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Services\MomoService;
use App\Services\VnpayService;
use App\Services\CustomerSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // ====================== VNPAY RETURN (redirect về site) ======================
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.hash_secret');
        $data = $request->all();

        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        ksort($data);
        $hashData = [];
        foreach ($data as $k => $v) {
            $hashData[] = $k . '=' . $v;
        }
        $hashData = implode('&', $hashData);
        $myHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($myHash !== $vnp_SecureHash) {
            return redirect()->route('users.checkout.show')->with('error', 'Sai chữ ký VNPAY');
        }

        $rsp    = $data['vnp_ResponseCode'] ?? '';
        $txnRef = $data['vnp_TxnRef'] ?? null;

        // Tìm payment theo txn_ref bạn đã lưu trong JSON
        $payment = Payment::where('request_payload->txn_ref', $txnRef)->first();

        if ($rsp === '00' && $payment) {
            // cập nhật trạng thái giao dịch
            $payment->update([
                'status'           => 'success',
                'callback_payload' => $data,
            ]);

            // Đánh dấu đơn đã thanh toán (idempotent)
            $this->markOrderPaid($payment, 'vnpay');
            return redirect()->route('users.checkout.show')->with('success', 'Thanh toán thành công');
        }

        return redirect()->route('users.checkout.show')->with('error', 'Thanh toán thất bại: ' . $rsp);
    }

    // ====================== VNPAY IPN (server-to-server) ======================
    public function vnpayIpn(Request $request, VnpayService $vnpay)
    {
        $params  = $request->all();
        $valid   = $vnpay->verify($params);
        $txnRef  = $params['vnp_TxnRef'] ?? null;
        $rsp     = $params['vnp_ResponseCode'] ?? '99';

        $payment = Payment::where('request_payload->txn_ref', $txnRef)->first();
        if (!$payment) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        if (!$valid) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        if ($rsp === '00') {
            $payment->update(['status' => 'success', 'callback_payload' => $params]);
            $ok = $this->markOrderPaid($payment, 'vnpay'); // idempotent
            return response()->json(['RspCode' => '00', 'Message' => $ok ? 'Confirm Success' : 'Already Paid']);
        }

        $payment->update(['status' => 'failed', 'callback_payload' => $params]);
        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Failed']);
    }

    // ====================== MOMO RETURN ======================
    public function momoReturn(Request $request, MomoService $momo)
    {
        $params  = $request->all();
        $valid   = isset($params['signature']) ? $momo->verify($params) : false;
        $txnRef  = $params['orderId'] ?? null;
        $code    = (int)($params['resultCode'] ?? 99);

        $payment = Payment::where('request_payload->txn_ref', $txnRef)->first();
        if (!$payment) {
            return view('payment.result', ['success' => false, 'msg' => 'Không tìm thấy giao dịch.']);
        }

        $payment->update(['callback_payload' => $params]);

        if ($valid && $code === 0) {
            $payment->update(['status' => 'success']);
            $this->markOrderPaid($payment, 'momo'); // idempotent
            return view('payment.result', ['success' => true, 'msg' => 'Thanh toán MoMo thành công!']);
        }

        $payment->update(['status' => 'failed']);
        return view('payment.result', ['success' => false, 'msg' => 'Thanh toán MoMo thất bại hoặc bị hủy.']);
    }

    // ====================== MOMO IPN ======================
    public function momoIpn(Request $request, MomoService $momo)
    {
        $params = $request->all();
        if (!$momo->verify($params)) {
            return response()->json(['resultCode' => 5, 'message' => 'Invalid signature'], 200);
        }

        $txnRef  = $params['orderId'] ?? null;
        $code    = (int)($params['resultCode'] ?? 99);

        $payment = Payment::where('request_payload->txn_ref', $txnRef)->first();
        if (!$payment) {
            return response()->json(['resultCode' => 1, 'message' => 'Order not found'], 200);
        }

        if ($code === 0) {
            $payment->update(['status' => 'success', 'callback_payload' => $params]);
            $this->markOrderPaid($payment, 'momo'); // idempotent
        } else {
            $payment->update(['status' => 'failed', 'callback_payload' => $params]);
        }

        return response()->json(['resultCode' => 0, 'message' => 'OK'], 200);
    }

    // ====================== Helpers ======================

    /**
     * Xác định Order từ Payment:
     * - Ưu tiên quan hệ $payment->order (nếu model Payment có).
     * - Fallback: request_payload.order_id hoặc request_payload.order_code.
     */
    protected function resolveOrderFromPayment(Payment $payment): ?Order
    {
        // 1) Nếu Payment đã có quan hệ order()
        if (method_exists($payment, 'order') && $payment->relationLoaded('order')) {
            return $payment->getRelation('order');
        }
        if (method_exists($payment, 'order')) {
            if ($o = $payment->order) return $o;
        }

        // 2) Fallback: cột order_id trực tiếp trên Payment (nếu có)
        if (!empty($payment->order_id)) {
            if ($o = Order::find($payment->order_id)) return $o;
        }

        // 3) Fallback: Order id/code trong JSON payload của Payment
        $payload   = $payment->request_payload ?? [];
        $orderId   = Arr::get($payload, 'order_id');
        $orderCode = Arr::get($payload, 'order_code');

        if ($orderId && $o = Order::find($orderId)) return $o;
        if ($orderCode && $o = Order::where('code', $orderCode)->first()) return $o;

        // 4) Cuối cùng: thử dùng chính txn_ref là code
        $txnRef = Arr::get($payload, 'txn_ref');
        if ($txnRef && $o = Order::where('code', $txnRef)->first()) return $o;

        return null;
    }

    /**
     * Đánh dấu đơn đã thanh toán + sync Customer.
     * Idempotent: nếu đã paid thì bỏ qua.
     */
    protected function markOrderPaid(Payment $payment, string $gateway): bool
    {
        return DB::transaction(function () use ($payment, $gateway) {
            // Lock nhẹ nhàng để tránh race IPN/Return đồng thời
            $order = $this->resolveOrderFromPayment($payment);
            if (!$order) {
                // Không tìm thấy đơn => vẫn trả thành công cho cổng để tránh bắn lại vô hạn,
                // nhưng bạn có thể log để xử lý thủ công.
                return false;
            }

            // Đã paid rồi thì thôi (idempotent)
            if (($order->payment_status ?? null) === 'paid') {
                return false;
            }

            // Cập nhật trạng thái thanh toán của đơn
            $order->payment_status = 'paid';
            // Nếu bạn có cột status và muốn chuyển bước xử lý: $order->status = 'processing';
            // Nếu có cột paid_at: $order->paid_at = now();  (đảm bảo cột này tồn tại rồi hãy dùng)
            $order->save();

            // Đồng bộ bảng customers (cộng orders_count, total_spent, ...)
            CustomerSyncService::touchFromOrder($order, true);

            // Ghi chú thêm về gateway (tuỳ bạn muốn log ở đâu)
            // $payment->update(['note' => trim(($payment->note ?? '') . " [paid_by:$gateway]")]);

            return true;
        });
    }
}

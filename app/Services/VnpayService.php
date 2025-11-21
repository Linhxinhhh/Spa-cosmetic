<?php
namespace App\Services;

use Illuminate\Support\Str;

class VnpayService
{
    private function buildHashData(array $data): string
    {
        ksort($data);
        $pairs = [];
        foreach ($data as $k => $v) {
            $pairs[] = $k . '=' . $v; // KHÔNG urlencode khi ký!
        }
        return implode('&', $pairs);
    }

    public function createPaymentUrl(array $p): string
    {
        $vnpUrl     = trim((string) config('services.vnpay.url'));
$tmnCode    = trim((string) config('services.vnpay.tmn_code'));
$hashSecret = trim((string) config('services.vnpay.hash_secret'));
$returnUrl  = trim((string) config('services.vnpay.return_url'));

        if (!$vnpUrl || !$tmnCode || !$hashSecret || !$returnUrl) {
            throw new \RuntimeException('Thiếu cấu hình VNPAY trong .env/services.php');
        }

        // Dùng txn_ref truyền vào (đã lưu DB), nếu chưa có thì mới generate
        $txnRef    = (string)($p['txn_ref'] ?? strtoupper(Str::random(12)));
        $amount    = (int)($p['amount'] ?? 0);
        $orderInfo = (string)($p['order_info'] ?? 'Order');

        $input = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $tmnCode,
            'vnp_Amount'     => $amount * 100,            // *100 bắt buộc
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => request()->ip(),
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => $orderInfo,               // bạn đang set = order_code
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $returnUrl,
            'vnp_TxnRef'     => $txnRef,
        ];

        // Ký HMAC SHA512 với chuỗi KHÔNG encode
        $hashData = $this->buildHashData($input);
        $secure   = hash_hmac('sha512', $hashData, $hashSecret);

        // Tạo query cho URL (encode chuẩn RFC 3986 để tránh lệch)
        $query = http_build_query($input, '', '&', PHP_QUERY_RFC3986);
        \Log::debug('VNPAY_ENV', [
  'url' => $vnpUrl,
  'tmn' => $tmnCode,
  'return' => $returnUrl,
]);
        // Log để debug khi cần
        \Log::debug('VNPAY_SIGN', ['hashData' => $hashData, 'signature' => $secure]);

        return $vnpUrl . '?' . $query . '&vnp_SecureHash=' . $secure;
    }

    public function verifyChecksum(array $params): bool
    {
        $secret = config('services.vnpay.hash_secret');
        if (!$secret || empty($params['vnp_SecureHash'])) return false;

        $data = $params;
        $vnp_SecureHash = $data['vnp_SecureHash'];
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        // Dùng cùng cách build như khi ký
        $hashData = $this->buildHashData($data);
        $calc = hash_hmac('sha512', $hashData, $secret);

        \Log::debug('VNPAY_VERIFY', ['hashData' => $hashData, 'calc' => $calc, 'recv' => $vnp_SecureHash]);
        return hash_equals($calc, $vnp_SecureHash);
    }
}

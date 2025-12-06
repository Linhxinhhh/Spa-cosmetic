<?php
namespace App\Services;

use Illuminate\Support\Str;

class VnpayService
{
    

    public function createPaymentUrl(array $p): string
{
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    $vnpUrl     = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    $tmnCode    = '4JN2S4S8';
    $hashSecret = 'a3cee559aa06182da69d13b7aae4c8c2';
    $returnUrl  = 'https://spa-cosmetic-copy-production.up.railway.app/users/cart';

    $txnRef    = $p['txn_ref'] ?? strtoupper(Str::random(12));
    $amount    = (int)($p['amount'] ?? 0);
    $orderInfo = "Thanh toan GD:" . ($p['order_info'] ?? 'Order');

    $startTime = now()->format('YmdHis');
    $expire    = now()->addMinutes(15)->format('YmdHis');

    $inputData = [
        "vnp_Version"    => "2.1.0",
        "vnp_TmnCode"    => $tmnCode,
        "vnp_Amount"     => $amount * 100,
        "vnp_Command"    => "pay",
        "vnp_CreateDate" => $startTime,
        "vnp_ExpireDate" => $expire,
        "vnp_CurrCode"   => "VND",
        "vnp_IpAddr"     => request()->ip() ?? '127.0.0.1',
        "vnp_Locale"     => 'vn',
        "vnp_OrderInfo"  => $orderInfo,   // ❗ KHÔNG encode
        "vnp_OrderType"  => "other",
        "vnp_ReturnUrl"  => $returnUrl,
        "vnp_TxnRef"     => $txnRef,
    ];

    // --- BUILD DATA TO HASH (CHUẨN VNPAY)
    ksort($inputData);
    $hashData = "";

    foreach ($inputData as $key => $value) {
        $hashData .= $key . "=" . $value . "&";   // ❗ Không urlencode
    }
    $hashData = rtrim($hashData, "&");

    // --- TẠO CHỮ KÝ
    $vnp_SecureHash = hash_hmac('sha512', $hashData, $hashSecret);

    // --- BUILD QUERY STRING CHO URL (CÓ urlencode)
    $query = http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);

    $vnp_Url = $vnpUrl . "?" . $query . "&vnp_SecureHash=" . $vnp_SecureHash;
dd($vnp_Url);
    return $vnp_Url;
}

    public function verifyChecksum(array $params): bool
    {
        $secret = 'a3cee559aa06182da69d13b7aae4c8c2';
        if (empty($params['vnp_SecureHash'])) {
            return false;
        }

        $receivedHash = $params['vnp_SecureHash'];
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        ksort($params);

        $hashData = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $calcHash = hash_hmac('sha512', $hashData, $secret);
        return hash_equals($calcHash, $receivedHash);
    }
}

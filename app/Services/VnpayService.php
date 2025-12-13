<?php
namespace App\Services;

use Illuminate\Support\Str;

class VnpayService
{


    public function createPaymentUrl(array $p): string
    {
        // 1. Cấu hình timezone
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        // 2. Khai báo dữ liệu cố định
        $vnpUrl = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        $tmnCode = '4JN2S4S8'; // Lấy chuẩn từ portal merchant của bạn
        $hashSecret = 'SI38CXSTJ02YML4FX31B4W2NAR8GH3JZ'; // Lấy chuẩn từ portal merchant của bạn
        $returnUrl  = 'https://spa-cosmetic-copy-production.up.railway.app/users/paysuccess';
        // 3. Khai báo dữ liệu động
        $txnRef = $p['txn_ref'] ?? strtoupper(bin2hex(random_bytes(6)));
        $amount = (int) ($p['amount'] ?? 0);
        $orderInfo = "Thanh toan GD " . ($p['order_info'] ?? 'Order');
        $startTime = date('YmdHis');

        // 4. Build tham số đầu vào (không có ký tự thừa, không rỗng, đúng chuẩn)
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $tmnCode,
            "vnp_Amount" => $amount * 100, // Đơn vị là đồng x 100
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $startTime,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $orderInfo, // KHÔNG encode, ký tự gốc
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $returnUrl,
            "vnp_TxnRef" => $txnRef,
        ];
        // 5. Xoá các trường giá trị rỗng (quan trọng, tránh NULL bị hash)
        foreach ($inputData as $k => $v) {
            if ($v === null || $v === '')
                unset($inputData[$k]);
        }
        // 6. Sắp xếp key tăng dần theo bảng chữ cái (rất quan trọng!)
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnpUrl . "?" . $query;
        if (isset($hashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $hashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
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

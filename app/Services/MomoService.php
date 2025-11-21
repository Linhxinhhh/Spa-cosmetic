<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MomoService
{
    public function createPayment(array $data): array
    {
        $endpoint    = config('services.momo.endpoint');
        $partnerCode = config('services.momo.partner_code');
        $accessKey   = config('services.momo.access_key');
        $secretKey   = config('services.momo.secret_key');
        $returnUrl   = config('services.momo.return_url');
        $ipnUrl      = config('services.momo.ipn_url');

        $payload = [
            'partnerCode' => $partnerCode,
            'accessKey'   => $accessKey,
            'requestId'   => $data['txn_ref'],
            'amount'      => (string)$data['amount'],
            'orderId'     => $data['txn_ref'],
            'orderInfo'   => $data['order_info'],
            'redirectUrl' => $returnUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'requestType' => 'captureWallet',
            'extraData'   => base64_encode(json_encode(['order_code'=>$data['order_code']])),
        ];

        // raw signature theo thứ tự field của MoMo
        $raw = "accessKey=$accessKey&amount={$payload['amount']}&extraData={$payload['extraData']}&ipnUrl=$ipnUrl&orderId={$payload['orderId']}&orderInfo={$payload['orderInfo']}&partnerCode=$partnerCode&redirectUrl=$returnUrl&requestId={$payload['requestId']}&requestType={$payload['requestType']}";
        $payload['signature'] = hash_hmac('sha256', $raw, $secretKey);

        $res = Http::asJson()->post($endpoint, $payload)->json();
        return $res; // gồm payUrl, deeplink, resultCode...
    }

    public function verify(array $params): bool
    {
        $secretKey = config('services.momo.secret_key');
        // Field xác minh theo doc MoMo IPN/return
        $raw = "accessKey={$params['accessKey']}&amount={$params['amount']}&extraData={$params['extraData']}&message={$params['message']}&orderId={$params['orderId']}&orderInfo={$params['orderInfo']}&orderType={$params['orderType']}&partnerCode={$params['partnerCode']}&payType={$params['payType']}&requestId={$params['requestId']}&responseTime={$params['responseTime']}&resultCode={$params['resultCode']}&transId={$params['transId']}";
        $calc = hash_hmac('sha256', $raw, $secretKey);
        return isset($params['signature']) && hash_equals($calc, $params['signature']);
    }
    public function verifySignature(array $params): bool
{
    $secretKey = config('services.momo.secret_key');
    if (!$secretKey || empty($params['signature'])) return false;

    // Các field cần cho rawHash (theo tài liệu MoMo Return)
    $orderId     = $params['orderId'] ?? '';
    $requestId   = $params['requestId'] ?? '';
    $amount      = $params['amount'] ?? '';
    $orderInfo   = $params['orderInfo'] ?? '';
    $orderType   = $params['orderType'] ?? '';
    $transId     = $params['transId'] ?? '';
    $resultCode  = $params['resultCode'] ?? '';
    $message     = $params['message'] ?? '';
    $payType     = $params['payType'] ?? '';
    $responseTime= $params['responseTime'] ?? '';

    $rawHash = "amount=$amount&message=$message&orderId=$orderId&orderInfo=$orderInfo"
             . "&orderType=$orderType&partnerCode=".(config('services.momo.partner_code'))
             . "&payType=$payType&requestId=$requestId&responseTime=$responseTime"
             . "&resultCode=$resultCode&transId=$transId";

    $calcSignature = hash_hmac('sha256', $rawHash, $secretKey);

    return hash_equals($calcSignature, $params['signature']);
}

}

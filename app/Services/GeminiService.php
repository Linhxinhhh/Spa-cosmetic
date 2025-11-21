<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    private $apiKey;
    private $model;
    private $endpoint;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');

        // ⚡ Model nhanh nhất thời điểm hiện tại
        $this->model = "models/gemini-2.0-flash-lite";

        $this->endpoint =
            "https://generativelanguage.googleapis.com/v1/{$this->model}:generateContent?key={$this->apiKey}";
    }

    public function generateText($prompt)
    {
        try {
            $response = Http::timeout(5)  // giới hạn xử lý 5s
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($this->endpoint, [
                    "contents" => [
                        [
                            "role" => "user",
                            "parts" => [
                                ["text" => $prompt]
                            ]
                        ]
                    ],
                    "generationConfig" => [
                        "temperature" => 0.7,
                        "topK" => 40,
                        "topP" => 0.9,
                        "maxOutputTokens" => 200
                    ],
                ]);

            // ❌ Nếu API lỗi
            if (!$response->successful()) {

                $error = $response->json()['error']['code'] ?? null;

                if ($error == 429) {
                    return "⚠️ Hệ thống đang quá tải. Vui lòng nhập rõ câu hỏi hơn hoặc thử lại sau vài giây.";
                }

                if ($error == 400 || $error == 404) {
                    return "⚠️ Hệ thống chưa hiểu yêu cầu của bạn, bạn vui lòng mô tả rõ hơn nhé!";
                }

                return "⚠️ Xin lỗi, hệ thống đang bận. Bạn vui lòng thử lại sau!";
            }

            // ✅ Xử lý dữ liệu trả về
            return $response->json()['candidates'][0]['content']['parts'][0]['text']
                ?? "⚠️ AI không nhận được phản hồi, vui lòng thử lại!";
        }

        // ❌ Trường hợp lỗi kết nối
        catch (\Exception $e) {
            return "⚠️ Mạng không ổn định. Vui lòng thử lại!";
        }
    }
}

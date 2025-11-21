<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class ChatbotTrainingSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================
        // 1 — Insert câu hỏi cố định
        // ==========================
        DB::table('chatbot_training')->insert([

            // ====== DA LIỄU – TƯ VẤN SẢN PHẨM ======
            [
                "question" => "Da dầu mụn nên dùng gì",
                "answer"   => "Da dầu mụn nên dùng: sữa rửa mặt dịu nhẹ, BHA 2%, Niacinamide, kem chống nắng oil-free.",
                "category" => "skin"
            ],
            [
                "question" => "Da khô nên dùng gì",
                "answer"   => "Da khô nên dùng: sữa rửa mặt cấp ẩm, HA, Ceramide, dưỡng ẩm đậm đặc.",
                "category" => "skin"
            ],
            [
                "question" => "Da hỗn hợp nên dùng gì",
                "answer"   => "Da hỗn hợp nên dùng sữa rửa mặt dịu nhẹ, toner cân bằng, niacinamide.",
                "category" => "skin"
            ],
            [
                "question" => "Da nhạy cảm nên dùng gì",
                "answer"   => "Da nhạy cảm nên dùng sản phẩm không hương liệu, Centella, Ceramide.",
                "category" => "skin"
            ],

            // ====== MỸ PHẨM – TƯ VẤN THEO GIÁ ======
            [
                "question" => "Sản phẩm dưới 500k",
                "answer"   => "Một số sản phẩm dưới 500k: COSRX, Klairs, Skin1004.",
                "category" => "product"
            ],

            // ====== ĐẶT LỊCH SPA ======
            [
                "question" => "Cách đặt lịch spa",
                "answer"   => "Bạn chỉ cần gửi: Họ tên – Số điện thoại – Ngày – Giờ – Dịch vụ muốn đặt.",
                "category" => "booking"
            ],
            [
                "question" => "Spa có mở cửa không",
                "answer"   => "Spa mở cửa từ 8:00 – 20:00 mỗi ngày.",
                "category" => "booking"
            ],

            // ====== DỊCH VỤ SPA GENERAL ======
            [
                "question" => "Gội đầu dưỡng sinh là gì",
                "answer"   => "Gội đầu dưỡng sinh giúp thư giãn, giảm stress, lưu thông khí huyết.",
                "category" => "service"
            ],

            // ====== TRA ĐƠN ======
            [
                "question" => "Tra đơn hàng",
                "answer"   => "Vui lòng gửi mã đơn (#1234) để mình kiểm tra.",
                "category" => "order"
            ],

            // ====== THÔNG TIN SPA ======
            [
                "question" => "Spa ở đâu",
                "answer"   => "Spa nằm tại địa chỉ: 123 Đường ABC, TP. XYZ.",
                "category" => "info"
            ],
            [
                "question" => "Giờ làm việc",
                "answer"   => "Spa làm việc từ 08:00–20:00 mỗi ngày.",
                "category" => "info"
            ],
        ]);

        // ===========================================
        // 2 — Insert tự động theo bảng dịch vụ thực tế
        // ===========================================
        $services = Service::all();

        foreach ($services as $s) {

            DB::table('chatbot_training')->insert([
                // #1: dịch vụ là gì
                [
                    "question" => "Dịch vụ {$s->service_name} là gì?",
                    "answer"   => $s->description ?? "Dịch vụ {$s->service_name} giúp thư giãn & chăm sóc da.",
                    "category" => "service"
                ],

                // #2: giá bao nhiêu
                [
                    "question" => "Giá dịch vụ {$s->service_name} bao nhiêu?",
                    "answer"   => "Giá dịch vụ {$s->service_name} là " . number_format($s->price, 0, ',', '.') . "₫.",
                    "category" => "service"
                ],

                // #3: thời gian thực hiện
                [
                    "question" => "Dịch vụ {$s->service_name} làm trong bao lâu?",
                    "answer"   => "Thời lượng dịch vụ {$s->service_name} khoảng {$s->duration} phút.",
                    "category" => "service"
                ],

                // #4: lợi ích
                [
                    "question" => "Lợi ích của dịch vụ {$s->service_name} là gì?",
                    "answer"   => $s->description ?? "Dịch vụ {$s->service_name} giúp thư giãn & làm đẹp da.",
                    "category" => "service"
                ],

                // #5: có phù hợp không
                [
                    "question" => "Tôi có nên làm dịch vụ {$s->service_name} không?",
                    "answer"   => "Nếu bạn muốn thư giãn hoặc chăm sóc da, dịch vụ {$s->service_name} là lựa chọn phù hợp.",
                    "category" => "service"
                ],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run()
    {
$services = [
            [
                'service_name' => 'Chăm sóc da chuyên sâu',
                'short_desc'   => 'Làm sạch – cấp ẩm – thư giãn',
                'category_id'  => 'SV015',
                'type'         => 'Gói',
                'effects'      => 'Da sáng – mịn – sạch sâu',
                'price'        => 399000,
                'price_original' => 450000,
                'price_sale'   => 399000,
                'duration'     => 60,
                'description'  => 'Làm sạch sâu – tẩy da chết – đắp mask – massage thư giãn giúp da sáng và khỏe hơn.',
                'thumbnail'    => '/uploads/services/chamsocda.jpg',
                'images'       => json_encode(['/uploads/services/chamsocda.jpg']),
                'status'       => 1,
                'is_active'    => 1,
                'is_featured'  => 1,
            ],
            [
                'service_name' => 'Liệu trình trị mụn chuẩn y khoa',
                'short_desc'   => 'Lấy nhân – kháng khuẩn – phục hồi',
                'category_id'  => 'SV013',
                'type'         => 'Gói',
                'effects'      => 'Giảm viêm – gom cồi – giảm mụn',
                'price'        => 550000,
                'price_original' => 620000,
                'price_sale'   => 550000,
                'duration'     => 75,
                'description'  => 'Quy trình trị mụn chuyên sâu gồm làm sạch, xông hơi, lấy nhân mụn an toàn, điện di kháng khuẩn.',
                'thumbnail'    => '/uploads/services/trimun.jpg',
                'images'       => json_encode(['/uploads/services/trimun.jpg']),
                'status'       => 1,
                'is_active'    => 1,
                'is_featured'  => 1,
            ],
            [
                'service_name' => 'Cấy trắng Vitamin C',
                'short_desc'   => 'Sáng da – đều màu – phục hồi',
                'category_id'  => 'SV015',
                'type'         => 'Lẻ',
                'effects'      => 'Da sáng bật tông',
                'price'        => 690000,
                'price_original' => 750000,
                'price_sale'   => 690000,
                'duration'     => 45,
                'description'  => 'Đưa Vitamin C tinh khiết vào da giúp sáng màu, mờ thâm, phục hồi da khỏe mạnh.',
                'thumbnail'    => '/uploads/services/caytrangvc.jpg',
                'images'       => json_encode(['/uploads/services/caytrangvc.jpg']),
                'status'       => 1,
                'is_active'    => 1,
                'is_featured'  => 0,
            ],
            [
    'service_name' => 'Gội đầu dưỡng sinh VIP',
    'short_desc'   => 'Thư giãn – giảm stress – ngủ ngon',
    'category_id'  => 'SV016',
    'type'         => 'Lẻ',
    'effects'      => 'Giảm căng thẳng, giảm đau đầu',
    'price'        => 180000,
    'price_original' => 220000,
    'price_sale'   => 180000,
    'duration'     => 40,
    'description'  => 'Gội đầu dưỡng sinh kết hợp bấm huyệt giúp giảm stress, hỗ trợ ngủ ngon.',
    'thumbnail'    => '/uploads/services/goidau_vip.jpg',
    'images'       => json_encode(['/uploads/services/goidau_vip.jpg']),
    'status'       => 1,
    'is_active'    => 1,
    'is_featured'  => 1,
],

[
    'service_name' => 'Ủ trắng phi thuyền Collagen',
    'short_desc'   => 'Bật tone – căng bóng – mịn màng',
    'category_id'  => 'SV006',
    'type'         => 'Lễ',
    'effects'      => 'Da bật 1–2 tone sau 1 buổi',
    'price'        => 490000,
    'price_original' => 550000,
    'price_sale'   => 490000,
    'duration'     => 60,
    'description'  => 'Công nghệ phi thuyền giúp dưỡng trắng sâu và phục hồi da tổn thương.',
    'thumbnail'    => '/uploads/services/utrach_collagen.jpg',
    'images'       => json_encode(['/uploads/services/utrach_collagen.jpg']),
    'status'       => 1,
    'is_active'    => 1,
    'is_featured'  => 0,
],

[
    'service_name' => 'Massage Body Thái',
    'short_desc'   => 'Giãn cơ – giảm đau – thư giãn',
    'category_id'  => 'SV016',
    'type'         => 'Lễ',
    'effects'      => 'Cơ thể nhẹ nhàng, thoải mái',
    'price'        => 350000,
    'price_original' => 400000,
    'price_sale'   => 350000,
    'duration'     => 60,
    'description'  => 'Kéo giãn cơ toàn thân theo phương pháp Thái giúp cơ thể thư giãn sâu.',
    'thumbnail'    => '/uploads/services/massage_thai.jpg',
    'images'       => json_encode(['/uploads/services/massage_thai.jpg']),
    'status'       => 1,
    'is_active'    => 1,
    'is_featured'  => 1,
],

[
    'service_name' => 'Tắm trắng sữa non',
    'short_desc'   => 'Mềm da – sáng tự nhiên',
    'category_id'  => 'SV015',
    'type'         => 'Lẻ',
    'effects'      => 'Da sáng mịn tự nhiên',
    'price'        => 390000,
    'price_original' => 450000,
    'price_sale'   => 390000,
    'duration'     => 45,
    'description'  => 'Dưỡng trắng chuyên sâu bằng sữa non giúp da mềm mịn tự nhiên.',
    'thumbnail'    => '/uploads/services/tamtrang_suanon.jpg',
    'images'       => json_encode(['/uploads/services/tamtrang_suanon.jpg']),
    'status'       => 1,
    'is_active'    => 1,
    'is_featured'  => 0,
],

[
    'service_name' => 'Triệt lông vĩnh viễn Laser DPL',
    'short_desc'   => 'Hiệu quả – an toàn – không đau',
    'category_id'  => 'SV009',
    'type'         => 'Lẻ',
    'effects'      => 'Giảm 80–90% lông sau 3 buổi',
    'price'        => 250000,
    'price_original' => 300000,
    'price_sale'   => 250000,
    'duration'     => 20,
    'description'  => 'Công nghệ Laser DPL triệt lông an toàn, không đau, hiệu quả lâu dài.',
    'thumbnail'    => '/uploads/services/trietlong.jpg',
    'images'       => json_encode(['/uploads/services/trietlong.jpg']),
    'status'       => 1,
    'is_active'    => 1,
    'is_featured'  => 1,
],

        ];
        foreach ($services as $service) {
            Service::create($service);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [

            [
                'product_name'     => 'Sữa rửa mặt BHA 2%',
                'variant_group'    => null,
                'brand_id'         => 1,
                'category_id'      => 2,
                'short_desc'       => 'Giảm dầu, làm sạch sâu, trị mụn đầu đen.',
                'price'            => 180000,
                'capacity'         => '150ml',
                'discount_percent' => 0,
                'discount_price'   => 180000,
                'description'      => 'Sữa rửa mặt chứa 2% BHA giúp làm sạch sâu, hỗ trợ trị mụn và se khít lỗ chân lông.',
                'specifications'   => 'Thành phần: BHA – Salicylic Acid; Dùng cho da dầu, da mụn',
                'stock_quantity'   => 100,
                'sold_quantity'    => 0,
                'is_featured'      => 1,
                'status'           => 1,
            ],

            [
                'product_name'     => 'Serum Vitamin C 15%',
                'variant_group'    => null,
                'brand_id'         => 1,
                'category_id'      => 3,
                'short_desc'       => 'Làm sáng da, mờ thâm mụn, đều màu.',
                'price'            => 350000,
                'capacity'         => '30ml',
                'discount_percent' => 10,
                'discount_price'   => 315000,
                'description'      => 'Serum Vitamin C giúp làm sáng da, mờ thâm và kích thích sản sinh collagen.',
                'specifications'   => 'Vitamin C tinh khiết 15%; phù hợp mọi loại da',
                'stock_quantity'   => 80,
                'sold_quantity'    => 0,
                'is_featured'      => 1,
                'status'           => 1,
            ],

            [
                'product_name'     => 'Kem dưỡng HA cấp ẩm 72h',
                'variant_group'    => null,
                'brand_id'         => 1,
                'category_id'      => 4,
                'short_desc'       => 'Dưỡng ẩm sâu – phục hồi da – mềm mịn.',
                'price'            => 260000,
                'capacity'         => '50g',
                'discount_percent' => 0,
                'discount_price'   => 260000,
                'description'      => 'Kem dưỡng chứa Hyaluronic Acid khóa ẩm sâu, phục hồi da trong 72h.',
                'specifications'   => 'Phù hợp da khô – hỗn hợp thiên khô.',
                'stock_quantity'   => 120,
                'sold_quantity'    => 0,
                'is_featured'      => 0,
                'status'           => 1,
            ],

            [
                'product_name'     => 'Kem chống nắng SPF50 PA++++',
                'variant_group'    => null,
                'brand_id'         => 1,
                'category_id'      => 5,
                'short_desc'       => 'Chống nắng mạnh – thấm nhanh – không nhờn.',
                'price'            => 199000,
                'capacity'         => '50ml',
                'discount_percent' => 0,
                'discount_price'   => 199000,
                'description'      => 'Kem chống nắng SPF50 bảo vệ da khỏi UVA/UVB và hạn chế thâm nám.',
                'specifications'   => 'Dùng cho mọi loại da.',
                'stock_quantity'   => 200,
                'sold_quantity'    => 0,
                'is_featured'      => 1,
                'status'           => 1,
            ],
        ];

        foreach ($products as $p) {
            $p['slug'] = Str::slug($p['product_name']);
            Product::create($p);
        }
    }
}

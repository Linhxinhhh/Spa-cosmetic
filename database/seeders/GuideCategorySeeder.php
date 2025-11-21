<?php
namespace Database\Seeders;

use App\Models\GuideCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuideCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Chăm sóc da cơ bản', 'slug' => 'cham-soc-da-co-ban', 'description' => 'Quy trình và kiến thức nền tảng.'],
            ['name' => 'Liệu trình công nghệ cao', 'slug' => 'lieu-trinh-cong-nghe-cao', 'description' => 'Các dịch vụ chuyên sâu tại Spa.'],
            ['name' => 'Review Sản phẩm', 'slug' => 'review-san-pham', 'description' => 'Đánh giá các sản phẩm làm đẹp.'],
            ['name' => 'Dinh dưỡng & Lối sống', 'slug' => 'dinh-duong-loi-song', 'description' => 'Mối liên hệ giữa sức khỏe và làm đẹp.'],
            ['name' => 'Trang điểm & Kỹ thuật', 'slug' => 'trang-diem-ky-thuat', 'description' => 'Hướng dẫn các phong cách và mẹo trang điểm cơ bản đến nâng cao.'],
            ['name' => 'Chăm sóc Tóc & Móng', 'slug' => 'cham-soc-toc-mong', 'description' => 'Các bí quyết và sản phẩm dành cho tóc khỏe, móng đẹp.'],
            ['name' => 'Sức khỏe Tinh thần & Thư giãn', 'slug' => 'suc-khoe-tinh-than', 'description' => 'Các bài viết về liệu pháp thư giãn, giảm stress và tác động lên da.'],
            ['name' => 'Trị liệu Body & Giảm béo', 'slug' => 'tri-lieu-body-giam-beo', 'description' => 'Kiến thức về các phương pháp chăm sóc cơ thể, định hình vóc dáng và giảm cân an toàn.'],
            ['name' => 'Mẹo vặt làm đẹp tại nhà', 'slug' => 'meo-vat-lam-dep-tai-nha', 'description' => 'Các công thức, phương pháp làm đẹp đơn giản có thể tự thực hiện tại nhà.'],
        ];

        foreach ($categories as $category) {
            // Kiểm tra và chỉ thêm nếu chưa tồn tại
            if (!GuideCategory::where('name', $category['name'])->exists()) {
                GuideCategory::create($category);
            }
        }
    }
    
}
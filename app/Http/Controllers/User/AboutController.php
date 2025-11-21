<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Service;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ProductImage;

class AboutController extends Controller
{
    /**
     * Trang Giới thiệu
     */
    public function index()
    {
        // ===== DỊCH VỤ NỔI BẬT =====
        $featuredServices = Cache::remember('about_featured_services', 600, function () {
            if (!class_exists(\App\Models\Service::class)) return collect();

            // Chỉ select các cột chắc chắn có; thumbnail thì thêm có điều kiện
            $svcQuery = Service::query()
                ->where('is_active', 1)
                ->when(schema_has_column('services', 'is_featured'), fn ($q) => $q->where('is_featured', 1))
                ->select(['service_id', 'service_name', 'slug'])
                ->latest('service_id')
                ->take(10);

            if (schema_has_column('services', 'thumbnail')) {
                $svcQuery->addSelect('thumbnail');
            }

            $items = $svcQuery->get();

            // Chuẩn hóa URL ảnh (nếu có thumbnail)
            $items->transform(function ($s) {
                $s->image_url = null;
                if (isset($s->thumbnail) && $s->thumbnail) {
                    $s->image_url = $this->toPublicUrl($s->thumbnail);
                }
                return $s;
            });

            return $items;
        });

        // ===== SẢN PHẨM NỔI BẬT (ẢNH LẤY TỪ product_images) =====
        $featuredProducts = Cache::remember('about_featured_products', 600, function () {
            if (!class_exists(\App\Models\Product::class)) return collect();

            // Subquery chọn 1 ảnh chính cho mỗi sản phẩm
            $mainImageSub = ProductImage::select('url')
                ->whereColumn('product_images.product_id', 'products.product_id')
                ->orderByDesc('is_main')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit(1);

            $prodQuery = Product::query()
                ->when(schema_has_column('products', 'status'), fn ($q) => $q->where('status', 1))
                ->when(schema_has_column('products', 'is_featured'), fn ($q) => $q->where('is_featured', 1))
                ->select('products.product_id', 'products.product_name', 'products.slug', 'products.price', 'products.discount_price')
                ->selectSub($mainImageSub, 'main_image')
                ->latest('products.product_id')
                ->take(8);

            $items = $prodQuery->get();

            // Chuẩn hoá URL ảnh từ 'main_image'
            $items->transform(function ($p) {
                $p->image_url = $p->main_image ? $this->toPublicUrl($p->main_image) : null;
                return $p;
            });

            return $items;
        });

        // ===== THƯƠNG HIỆU (LOGO WALL) =====
        $brands = Cache::remember('about_brands', 600, function () {
            if (!class_exists(\App\Models\Brand::class)) return collect();

            $items = Brand::query()
                ->select(['brand_id', 'brand_name'])
                ->orderBy('brand_name')
                ->take(12)
                ->get();

            // Nếu có cột 'logo' thì add và chuẩn hoá URL
            if (schema_has_column('brands', 'logo')) {
                $items = Brand::query()
                    ->select(['brand_id', 'brand_name', 'logo'])
                    ->orderBy('brand_name')
                    ->take(12)
                    ->get()
                    ->transform(function ($b) {
                        $b->image_url = isset($b->logo) ? $this->toPublicUrl($b->logo) : null;
                        return $b;
                    });
            }

            return $items;
        });

        // KPI (có thể đổi sang lấy từ DB nếu muốn)
        $kpis = [
            'customers' => (int) (config('site.kpi.customers', 12500)),
            'years'     => (int) (config('site.kpi.years', 8)),
            'clinics'   => (int) (config('site.kpi.clinics', 3)),
            'stars'     => (int) (config('site.kpi.stars', 2400)),
        ];

        // SEO meta (tuỳ chọn)
        $meta = [
            'title'       => config('app.name') . ' - Giới thiệu',
            'description' => 'Hệ sinh thái chăm sóc sắc đẹp: điều trị da, spa thư giãn và sản phẩm chăm sóc tại nhà.',
        ];

        return view('Users.reviews.index', compact(
            'featuredServices',
            'featuredProducts',
            'brands',
            'kpis',
            'meta'
        ));
    }

    /**
     * Chuẩn hoá đường dẫn ảnh thành URL public:
     * - Nếu đã là http(s) hoặc bắt đầu bằng '/', giữ nguyên.
     * - Còn lại: coi như path của Storage và trả về Storage::url().
     */
    private function toPublicUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }
        return Storage::url($path);
    }
}

/**
 * Helper nhỏ kiểm tra cột có tồn tại không (tránh lỗi khi DB chưa có cột).
 * Có thể đưa function này vào AppServiceProvider nếu thích.
 */
if (!function_exists('schema_has_column')) {
    function schema_has_column(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Service;

class RAGSearchService
{
    public function buildContext(string $query, int $limit = 5): string
    {
        $q = mb_strtolower(trim($query));

        // Từ khoá về da liễu
        $skinKeywords = [
            'mụn', 'mụn viêm', 'mụn đầu đen', 'mụn ẩn',
            'da dầu', 'da khô', 'da hỗn hợp', 'lỗ chân lông',
            'nám', 'tàn nhang', 'thâm', 'xỉn màu', 'lão hoá'
        ];

        // Match bằng từ khóa
        $shouldMatchSkin = collect($skinKeywords)->contains(fn($kw) => str_contains($q, $kw));

        /* ==========================
         *  QUERY SẢN PHẨM
         * ========================== */
        $products = Product::query()
            ->when($shouldMatchSkin, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('description', 'like', "%{$q}%")
                      ->orWhere('specifications', 'like', "%{$q}%")
                      ->orWhere('short_desc', 'like', "%{$q}%")
                      ->orWhere('product_name', 'like', "%{$q}%");
                });
            })
            ->active()
            ->orderByDesc('is_featured')
            ->limit($limit)
            ->get([
                'product_name', 'price', 'discount_price', 'capacity',
                'short_desc', 'specifications'
            ]);

        /* ==========================
         *  QUERY DỊCH VỤ SPA
         * ========================== */
        $services = Service::query()
            ->when($shouldMatchSkin, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('service_name', 'like', "%{$q}%")
                      ->orWhere('short_desc', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%")
                      ->orWhere('effects', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('is_featured')
            ->limit($limit)
            ->get([
                'service_name', 'price', 'duration', 'short_desc',
                'description', 'effects'
            ]);

        /* ==========================
         *  BUILD CONTEXT
         * ========================== */
        $ctx = [];

        if ($products->isNotEmpty()) {
            $ctx[] = "SẢN PHẨM PHÙ HỢP:\n" . $products->map(function ($p) {
                $gia = $p->discount_price ?: $p->price;

                return "- {$p->product_name}  
  Giá: " . number_format($gia, 0, ',', '.') . "₫  
  Mô tả: {$p->short_desc}";
            })->implode("\n\n");
        }

        if ($services->isNotEmpty()) {
            $ctx[] = "DỊCH VỤ SPA PHÙ HỢP:\n" . $services->map(function ($s) {
                return "- {$s->service_name}  
  Giá: " . number_format($s->price, 0, ',', '.') . "₫  
  Thời lượng: {$s->duration} phút  
  Hiệu quả: {$s->effects}";
            })->implode("\n\n");
        }

        return $ctx ? implode("\n\n", $ctx) : "Không tìm thấy dữ liệu liên quan.";
    }
    
}


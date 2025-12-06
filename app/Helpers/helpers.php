<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

if (!function_exists('product_img_url')) {
    function product_img_url($item)
    {
        $p = $item->thumbnail ?: $item->images;
        
        return $p;
    }
}

if (!function_exists('banner_img_url')) {
    function banner_img_url($banner)
    {        
        return !$banner || $banner->image ? Storage::disk('r2')->temporaryUrl($banner->image, now()->addMinutes(5)) : "http://www.nhadattanphu.xyz/Content/images/noImage.png";
    }
}

if (!function_exists('product_img')) {
    function product_img($p)
    {
        $val = $p->thumbnail ?: $p->images;
        if (!$val)
            return asset('images/placeholder-4x3.jpg');

        // JSON array
        if (is_string($val) && Str::startsWith(trim($val), '[')) {
            $arr = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($arr) && count($arr)) {
                $val = $arr[0];
            }
        }
        // Chuỗi nhiều ảnh
        if (is_string($val) && str_contains($val, ',')) {
            $parts = array_map('trim', explode(',', $val));
            $val = $parts[0] ?? $val;
        }
        // Mảng
        if (is_array($val)) {
            $val = $val[0] ?? null;
        }

        if (!$val)
            return asset('images/placeholder-4x3.jpg');

        if (Str::startsWith($val, ['http://', 'https://', '//']))
            return $val;

        $path = ltrim($val, '/');
        $path = preg_replace('#^(storage/|public/)#', '', $path);
        return Storage::disk('public')->exists($path) ? Storage::url($path) : asset($path);
    }
}

/** Tính giá cuối cùng */
if (!function_exists('final_price')) {
    function final_price($p)
    {
        $price = (float) ($p->price ?? 0);
        if ($p->discount_price && $p->discount_price > 0 && $p->discount_price < $price) {
            return (float) $p->discount_price;
        }
        if ($p->discount_percent && $p->discount_percent > 0) {
            return round($price * (100 - (float) $p->discount_percent) / 100, 2);
        }
        return $price;
    }
}

if (!function_exists('asset_from_mixed')) {
    function asset_from_mixed($path, $placeholder = 'images/placeholder-4x3.jpg')
    {
        if (!$path)
            return asset($placeholder);
        if (Str::startsWith($path, ['http://', 'https://', '//']))
            return $path;
        $p = ltrim(preg_replace('#^(public/|storage/)#', '', $path), '/');
        return Storage::disk('public')->exists($p) ? Storage::url($p) : asset($p);
    }
}

if (!function_exists('product_image_candidates')) {
    function product_image_candidates($item)
    {
        $list = [];
        if (method_exists($item, 'imagesRel')) {
            $rels = $item->relationLoaded('imagesRel') ? $item->imagesRel : $item->imagesRel()->get();
            foreach ($rels as $img) {
                $src = asset_from_mixed($img->path ?? $img->image ?? $img->url ?? null);
                if ($src)
                    $list[] = $src;
            }
        }
        if (empty($list) && !empty($item->images)) {
            $raw = $item->images;
            if (is_string($raw) && Str::startsWith(trim($raw), '['))
                $arr = json_decode($raw, true) ?: [];
            else
                $arr = is_array($raw) ? $raw : array_filter(array_map('trim', explode(',', (string) $raw)));
            foreach ($arr as $p) {
                $src = asset_from_mixed($p);
                if ($src)
                    $list[] = $src;
            }
        }
        if (!empty($item->thumbnail))
            array_unshift($list, asset_from_mixed($item->thumbnail));
        if (!$list)
            $list[] = asset('images/placeholder-4x3.jpg');
        return array_values(array_unique($list));
    }
}

if (!function_exists('product_main_src')) {
    function product_main_src($product)
    {
        $img = $product->imagesRel->first();

        return $img?->url ? Storage::disk('r2')->temporaryUrl($img->url, now()->addMinutes(5)) : "http://www.nhadattanphu.xyz/Content/images/noImage.png";
    }
}

if (!function_exists('product_hover_src')) {
    function product_hover_src($product)
    {
         $img = $product->imagesRel->skip(1)->first();

    // Nếu không có ảnh, trả về ảnh mặc định
   return $img?->url ? Storage::disk('r2')->temporaryUrl($img->url, now()->addMinutes(5)) : "http://www.nhadattanphu.xyz/Content/images/noImage.png";
    }
}
if (!function_exists('src_img_get')) {
    function src_img_get($url)
    {
        $fallback = 'http://www.nhadattanphu.xyz/Content/images/noImage.png';

        if (!$url) {
            return $fallback;
        }

        // Nếu đã là full URL R2 → TRẢ LUÔN
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        // Nếu bạn có public domain trong .env
        if ($domain = env('R2_PUBLIC_DOMAIN')) {
            return rtrim($domain, '/') . '/' . ltrim($url, '/');
        }

        // Trường hợp bucket private
        try {
            return Storage::disk('r2')->temporaryUrl(
                $url,
                now()->addMinutes(10)
            );
        } catch (\Throwable $e) {
            return $fallback;
        }
    }
}



if (!function_exists('product_final_price')) {
    function product_final_price($p)
    {
        $price = (float) ($p->price ?? 0);
        if ($p->discount_price && $p->discount_price > 0 && $p->discount_price < $price)
            return (float) $p->discount_price;
        if ($p->discount_percent && $p->discount_percent > 0)
            return round($price * (100 - (float) $p->discount_percent) / 100, 2);
        return $price;
    }
}
if (!function_exists('service_image')) {
    function service_image($service, $fallback = true)
    {
        if (is_string($service) && filter_var($service, FILTER_VALIDATE_URL)) {
            return $service;
        }

        if (!is_object($service)) {
            return $fallback ?  "http://www.nhadattanphu.xyz/Content/images/noImage.png": null;
        }

        // Ưu tiên thumbnail    
        if (!empty($service->thumbnail)) {
            return src_img_get($service->thumbnail);
        }

        // Lấy ảnh đầu tiên từ images (an toàn tuyệt đối)
        $firstImage = optional($service->images)->first();

        if ($firstImage?->image_url) {
            return src_img_get($firstImage->image_url);
        }

        return $fallback ? 'http://www.nhadattanphu.xyz/Content/images/noImage.png': null;
    }
}
function r2_url($path)
{
    return rtrim(env('R2_PUBLIC_URL'), '/') . '/' . ltrim($path, '/');
}


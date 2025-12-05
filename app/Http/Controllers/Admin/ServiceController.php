<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ServiceController extends Controller
{
    private const MAX_GALLERY_IMAGES = 6;

    private function mapType(?string $t): ?string
    {
        if (!$t) return null;
        $x = mb_strtolower(trim($t), 'UTF-8');
        return match ($x) {
            'single', 'le', 'lẻ' => 'Lẻ',
            'combo', 'goi', 'gói' => 'Gói',
            default => null,
        };
    }

    private function uploadToR2($file, string $folder, int $serviceId): string
    {
        $path = Storage::disk('r2')->put("services/{$serviceId}/{$folder}", $file);

        return env('R2_PUBLIC_DOMAIN')
            ? rtrim(env('R2_PUBLIC_DOMAIN'), '/') . '/' . $path
            : Storage::disk('r2')->url($path);
    }

    // ... index, create giữ nguyên như cũ ...

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_name'    => 'required|string|max:255',
            'short_desc'      => 'nullable|string|max:255',
            'category_id'     => 'required|exists:service_categories,category_id',
            'type'            => ['required', Rule::in(['Lẻ','Gói'])],
            'slug'            => 'nullable|string|max:255|unique:services,slug',
            'price'           => 'nullable|numeric|min:0',
            'price_original'  => 'nullable|numeric|min:0',
            'price_sale'      => 'nullable|numeric|min:0|lte:price_original',
            'duration'        => 'required|integer|min:1',
            'description'     => 'nullable|string',
            'status'          => 'required|boolean',
            'is_featured'     => 'nullable|boolean',

            'thumbnail'       => ['nullable', File::image()->types(['jpg','jpeg','png','webp','gif'])->max(5120)],
            'gallery'         => ['nullable', 'array', 'max:' . self::MAX_GALLERY_IMAGES],
            'gallery.*'       => ['nullable', File::image()->types(['jpg','jpeg','png','webp','gif'])->max(5120)],
        ]);

        $data['type'] = $this->mapType($data['type']) ?? 'Lẻ';
        if (blank($data['slug'] ?? null)) $data['slug'] = null;
        $data['status'] = $request->boolean('status');
        $data['is_featured'] = $request->boolean('is_featured');

        // Tạo service trước để có ID
        $service = Service::create($data);

        $galleryUrls = [];

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $url = $this->uploadToR2($request->file('thumbnail'), 'thumbnails', $service->service_id);
            $service->thumbnail = $url;
        }

        // gallery
        if ($request->hasFile('gallery')) {
            $files = array_slice($request->file('gallery'), 0, self::MAX_GALLERY_IMAGES);
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $url = $this->uploadToR2($file, 'gallery', $service->service_id);
                    $galleryUrls[] = $url;
                }
            }
        }

        // Nếu không có thumbnail → lấy ảnh đầu trong gallery làm thumbnail
        if (!$service->thumbnail && !empty($galleryUrls)) {
            $service->thumbnail = $galleryUrls[0];
        }

        $service->images = $galleryUrls; // mảng URL
        $service->save();

        return redirect()->route('admin.services.index')->with('success', 'Thêm dịch vụ thành công!');
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $data = $request->validate([
            'service_name'    => 'sometimes|required|string|max:255',
            'short_desc'      => 'nullable|string|max:255',
            'category_id'     => 'sometimes|required|exists:service_categories,category_id',
            'type'            => ['sometimes', Rule::in(['Lẻ','Gói'])],
            'slug'            => ['nullable', 'string', 'max:255', Rule::unique('services','slug')->ignore($id,'service_id')],
            'price'           => 'nullable|numeric|min:min0',
            'price_original'  => 'nullable|numeric|min:0',
            'price_sale'      => 'nullable|numeric|min:0|lte:price_original',
            'duration'        => 'sometimes|required|integer|min:1',
            'description'     => 'nullable|string',
            'status'          => 'sometimes|boolean',
            'is_featured'     => 'nullable|boolean',

            'thumbnail'       => ['nullable', File::image()->types(['jpg','jpeg','png','webp','gif'])->max(5120)],
            'gallery'         => ['nullable', 'array', 'max:' . self::MAX_GALLERY_IMAGES],
            'gallery.*'       => ['nullable', File::image()->types(['jpg','jpeg','png','webp','gif'])->max(5120)],

            // Danh sách URL muốn xóa (từ checkbox)
            'delete_images'   => ['nullable', 'array'],
            'delete_images.*' => ['url'],
        ]);

        $data['type'] = $this->mapType($data['type'] ?? null) ?? $service->type;
        if (isset($data['slug']) && blank($data['slug'])) $data['slug'] = null;
        $data['status'] = $request->boolean('status', $service->status);
        $data['is_featured'] = $request->boolean('is_featured', $service->is_featured);

        // === XÓA ẢNH PHỤ ===
        $currentGallery = is_array($service->images) ? $service->images : [];
        if ($request->has('delete_images')) {
            $toDelete = $request->delete_images;
            $currentGallery = array_filter($currentGallery, fn($url) => !in_array($url, $toDelete));

            // Xóa file trên R2
            foreach ($toDelete as $url) {
                $path = str_replace(env('R2_PUBLIC_DOMAIN', '') . '/', '', $url);
                if (Storage::disk('r2')->exists($path)) {
                    Storage::disk('r2')->delete($path);
                }
            }
        }

        // === UPLOAD ẢNH MỚI ===
        $newGallery = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file->isValid()) {
                    $url = $this->uploadToR2($file, 'gallery', $service->service_id);
                    $newGallery[] = $url;
                }
            }
        }

        // === THUMBNAIL MỚI ===
        if ($request->hasFile('thumbnail')) {
            // Xóa cũ
            if ($service->thumbnail) {
                $old = str_replace(env('R2_PUBLIC_DOMAIN', '') . '/', '', $service->thumbnail);
                Storage::disk('r2')->delete($old);
            }
            $service->thumbnail = $this->uploadToR2($request->file('thumbnail'), 'thumbnails', $service->service_id);
        }

        // Gộp lại gallery mới = cũ (sau khi xóa) + mới upload
        $finalGallery = array_merge($currentGallery, $newGallery);

        // Nếu mất thumbnail → lấy ảnh đầu tiên trong gallery (nếu có)
        if (!$service->thumbnail && !empty($finalGallery)) {
            $service->thumbnail = $finalGallery[0];
        }

        // Lưu
        $service->images = array_values($finalGallery); // re-index
        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Cập nhật dịch vụ thành công!');
    }


    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        // Xóa thumbnail
        
        if ($service->thumbnail) {
            $path = str_replace(env('R2_PUBLIC_DOMAIN', '') . '/', '', $service->thumbnail);
            Storage::disk('r2')->delete($path);
        }

        // Xóa hết gallery
        if (is_array($service->images)) {
            foreach ($service->images as $url) {
                $path = str_replace(env('R2_PUBLIC_DOMAIN', '') . '/', '', $url);
                Storage::disk('r2')->delete($path);
            }
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Xóa dịch vụ thành công!');
    }
}
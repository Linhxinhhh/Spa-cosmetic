<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceImage;
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

    // ================================== INDEX ==================================
    public function index(Request $request)
    {
        $services = Service::with(['category', 'images' => fn($q) => $q->orderBy('sort_order')])
            ->when($request->q, fn($q) => $q->where('service_name', 'like', "%{$request->q}%")
                                          ->orWhere('short_desc', 'like', "%{$request->q}%"))
            ->when($request->status !== null, fn($q) => $q->where('status', $request->status))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->type, fn($q) => $q->where('type', $this->mapType($request->type)))
            ->when($request->featured, fn($q) => $q->where('is_featured', 1))
            ->latest('service_id')
            ->paginate(10);

        $categories = ServiceCategory::whereNotNull('parent_id')
            ->orderBy('category_name')
            ->get(['category_id', 'category_name']);

        return view('dashboard.services.index', compact('services', 'categories'));
    }

    // ================================== CREATE ==================================
    public function create()
    {
        $categories = ServiceCategory::whereNotNull('parent_id')->orderBy('category_name')->get();
        return view('dashboard.services.create', compact('categories'));
    }

    // ================================== EDIT ==================================
    public function edit($id)
    {
        $service = Service::with('images')->findOrFail($id);
        $categories = ServiceCategory::whereNotNull('parent_id')->orderBy('category_name')->get();
        return view('dashboard.services.edit', compact('service', 'categories'));
    }

    // ================================== STORE ==================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'service_name'    => 'required|string|max:255',
            'short_desc'      => 'nullable|string|max:255',
            'category_id'     => 'required|exists:service_categories,category_id',
            'type'            => ['required', Rule::in(['Lẻ', 'Gói'])],
            'slug'            => 'nullable|string|max:255|unique:services,slug',
            'price'           => 'nullable|numeric|min:0',
            'price_original'  => 'nullable|numeric|min:0',
            'price_sale'      => 'nullable|numeric|min:0|lte:price_original',
            'duration'        => 'required|integer|min:1',
            'description'     => 'nullable|string',
            'status'          => 'required|boolean',
            'is_featured'     => 'nullable|boolean',

            'thumbnail' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp', 'gif'])->max(5120)],
            'gallery'   => ['nullable', 'array', 'max:' . self::MAX_GALLERY_IMAGES],
            'gallery.*' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp', 'gif'])->max(5120)],
        ]);

        $data['type'] = $this->mapType($data['type']) ?? 'Lẻ';
        $data['slug'] = blank($data['slug'] ?? null) ? null : $data['slug'];
        $data['status'] = $request->boolean('status');
        $data['is_featured'] = $request->boolean('is_featured');

        $service = Service::create($data);

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $url = $this->uploadToR2($request->file('thumbnail'), 'thumbnails', $service->service_id);
            $service->thumbnail = $url;
            $service->save();
        }

        // Upload gallery vào bảng service_images
        if ($request->hasFile('gallery')) {
            $files = array_slice($request->file('gallery'), 0, self::MAX_GALLERY_IMAGES);
            foreach ($files as $index => $file) {
                if ($file->isValid()) {
                    $url = $this->uploadToR2($file, 'gallery', $service->service_id);
                    $service->images()->create([
                        'image_url'  => $url,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        // Nếu không có thumbnail → lấy ảnh đầu làm thumbnail
        if (!$service->thumbnail && $service->images()->exists()) {
            $service->thumbnail = $service->images()->first()->image_url;
            $service->save();
        }

        return redirect()->route('admin.services.index')->with('success', 'Thêm dịch vụ thành công!');
    }

    // ================================== UPDATE ==================================
    public function update(Request $request, $id)
    {
        $service = Service::with('images')->findOrFail($id);

        $data = $request->validate([
            'service_name'    => 'sometimes|required|string|max:255',
            'short_desc'      => 'nullable|string|max:255',
            'category_id'     => 'sometimes|required|exists:service_categories,category_id',
            'type'            => 'sometimes|required|in:Lẻ,Gói',
            'slug'            => ['nullable', 'string', 'max:255', Rule::unique('services', 'slug')->ignore($id, 'service_id')],
            'price'           => 'nullable|numeric|min:0',
            'price_original'  => 'nullable|numeric|min:0',
            'price_sale'      => 'nullable|numeric|min:0|lte:price_original',
            'duration'        => 'sometimes|required|integer|min:1',
            'description'     => 'nullable|string',
            'status'          => 'sometimes|required|boolean',
            'is_featured'     => 'nullable|boolean',

            'thumbnail'       => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp', 'gif'])->max(5120)],
            'gallery'         => ['nullable', 'array', 'max:' . self::MAX_GALLERY_IMAGES],
            'gallery.*'       => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp', 'gif'])->max(5120)],
            'delete_images'   => ['nullable', 'array'],
            'delete_images.*' => ['numeric', 'exists:service_images,id'],
        ]);

        $data['type'] = $this->mapType($data['type'] ?? null) ?? $service->type;
        $data['slug'] = isset($data['slug']) && blank($data['slug']) ? null : ($data['slug'] ?? $service->slug);
        $data['status'] = $request->boolean('status', $service->status);
        $data['is_featured'] = $request->boolean('is_featured', $service->is_featured);

        $service->update($data);

        // XÓA ẢNH GALLERY
        if ($request->filled('delete_images')) {
            $deleted = $service->images()->whereIn('id', $request->delete_images)->get();
            foreach ($deleted as $img) {
                $path = str_replace(env('R2_PUBLIC_DOMAIN', '') . '/', '', $img->image_url);
                Storage::disk('r2')->delete($path);
                $img->delete();
            }

            // Nếu thumbnail bị xóa theo → lấy lại ảnh đầu
            if ($service->thumbnail && $deleted->pluck('image_url')->contains($service->thumbnail)) {
                $first = $service->images()->first();
                $service->thumbnail = $first?->image_url;
                $service->save();
            }
        }

        // UPLOAD ẢNH MỚI
        if ($request->hasFile('gallery')) {
            $slotLeft = self::MAX_GALLERY_IMAGES - $service->images()->count();
            $files = array_slice($request->file('gallery'), 0, $slotLeft);

            foreach ($files as $index => $file) {
                if ($file->isValid()) {
                    $url = $this->uploadToR2($file, 'gallery', $service->service_id);
                    $service->images()->create([
                        'image_url'  => $url,
                        'sort_order' => $service->images()->max('sort_order') + $index + 1,
                    ]);
                }
            }
        }

        // THAY THUMBNAIL MỚI
        if ($request->hasFile('thumbnail')) {
            if ($service->thumbnail) {
                $old = str_replace(env('R2_PUBLIC_DOMAIN', '') . '/', '', $service->thumbnail);
                Storage::disk('r2')->delete($old);
            }
            $url = $this->uploadToR2($request->file('thumbnail'), 'thumbnails', $service->service_id);
            $service->thumbnail = $url;
            $service->save();
        }

        // Nếu mất thumbnail → lấy ảnh đầu trong gallery
        if (!$service->thumbnail && $service->images()->exists()) {
            $service->thumbnail = $service->images()->first()->image_url;
            $service->save();
        }

        return redirect()->route('admin.services.index')->with('success', 'Cập nhật dịch vụ thành công!');
    }

    // ================================== DESTROY ==================================
    public function destroy($id)
    {
        $service = Service::with('images')->findOrFail($id);

        // Xóa thumbnail
        if ($service->thumbnail) {
            $path = str_replace(env('R2_PUBLIC_DOMAIN', '') . '/', '', $service->thumbnail);
            Storage::disk('r2')->delete($path);
        }

        // Xóa tất cả ảnh gallery
        foreach ($service->images as $img) {
            $path = str_replace(env('R2_PUBLIC_DOMAIN', '') . '/', '', $img->image_url);
            Storage::disk('r2')->delete($path);
            $img->delete();
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Xóa dịch vụ thành công!');
    }
}
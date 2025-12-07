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



    // ================================== INDEX ==================================
    public function index(Request $request)
{
    
    $query = Service::query();

    // QUAN TRỌC: SỬA DÒNG NÀY
   $services = Service::with(['images' => function($q) {
    $q->orderBy('sort_order')->limit(3);
}])->paginate(10);

    $categories = ServiceCategory::orderBy('category_name')->get();
    
    // Search
    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function($q) use ($search) {
            $q->where('service_name', 'like', "%{$search}%")
              ->orWhere('short_desc', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Filters
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }
    
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }
    
    if ($request->filled('featured')) {
        $query->where('is_featured', 1);
    }

    return view('dashboard.services.index', compact('services', 'categories'));
}

    // ================================== CREATE ==================================
    public function create()
    {
        $categories = ServiceCategory::whereNotNull('parent_id')->orderBy('category_name')->get();
        return view('dashboard.services.create', compact('categories'));
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
            'price_original'  => 'nullable|numeric|min:0',
            'price_sale'      => 'nullable|numeric|min:0',
            'duration'        => 'required|integer|min:1',
            'description'     => 'nullable|string',
            'status'          => 'required|in:0,1',
            'is_featured'     => 'nullable|boolean',

            'thumbnail' => ['nullable', File::image()->types(['jpg','jpeg','png','webp','gif'])->max(5120)],
            'gallery'   => ['nullable', 'array', 'max:' . self::MAX_GALLERY_IMAGES],
            'gallery.*' => ['nullable', File::image()->types(['jpg','jpeg','png','webp','gif'])->max(5120)],
        ]);

        $data['type'] = $this->mapType($data['type']) ?? 'Lẻ';
        $data['slug'] = blank($data['slug'] ?? null) ? null : $data['slug'];
        $data['status'] = (int) $data['status'];
        $data['is_featured'] = $request->boolean('is_featured');

        // Tạo dịch vụ trước để có service_id
        $service = Service::create($data);

        $maxSort = -1;
        $newMainId = null;

        // Ảnh đại diện (thumbnail field trong bảng services)
        if ($request->hasFile('thumbnail')) {
            $url = $this->storeImageFile($request->file('thumbnail'), $service->service_id, 'thumbnails');
            $service->thumbnail = $url;
            $service->save();
        }

        // Ảnh chính (lưu vào bảng service_images, có is_main = true)
        if ($request->hasFile('images')) {
            $url = $this->storeImageFile($request->file('images'), $service->service_id, 'main');
            $img = ServiceImage::create([
                'service_id' => $service->service_id,
                'image_url'  => $url,
                'sort_order' => ++$maxSort,
                'is_main'    => true,
            ]);
            $newMainId = $img->id;
        }

        // Gallery ảnh phụ
        if ($request->hasFile('gallery')) {
            $files = array_slice($request->file('gallery'), 0, self::MAX_GALLERY_IMAGES);
            foreach ($files as $file) {
                if (!$file->isValid()) continue;

                $url = $this->storeImageFile($file, $service->service_id, 'gallery');
                $img = ServiceImage::create([
                    'service_id' => $service->service_id,
                    'image_url'  => $url,
                    'sort_order' => ++$maxSort,
                    'is_main'    => false,
                ]);

                // Nếu chưa có ảnh chính → lấy ảnh đầu tiên trong gallery làm main
                if ($newMainId === null) {
                    $newMainId = $img->id;
                }
            }
        }

        // Đảm bảo luôn có 1 ảnh is_main = true
        if ($newMainId) {
            ServiceImage::where('service_id', $service->service_id)
                ->update(['is_main' => false]);
            ServiceImage::where('id', $newMainId)->update(['is_main' => true]);
        } elseif (!ServiceImage::where('service_id', $service->service_id)->where('is_main', true)->exists()) {
            $first = ServiceImage::where('service_id', $service->service_id)->orderBy('sort_order')->first();
            if ($first) $first->update(['is_main' => true]);
        }

        return redirect()->route('admin.services.index')->with('success', 'Thêm dịch vụ thành công!');
    }

    // ================================== EDIT ==================================
    public function edit($id)
    {
        $service = Service::with(['images' => fn($q) => $q->orderBy('sort_order')])->findOrFail($id);
        $categories = ServiceCategory::whereNotNull('parent_id')->orderBy('category_name')->get();

        return view('dashboard.services.edit', compact('service', 'categories'));
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
            'slug'            => ['nullable', 'string', 'max:255', Rule::unique('services','slug')->ignore($id, 'service_id')],
            'price_original'  => 'nullable|numeric|min:0',
            'price_sale'      => 'nullable|numeric|min:0',
            'duration'        => 'sometimes|required|integer|min:1',
            'description'     => 'nullable|string',
            'status'          => 'sometimes|required|in:0,1',
            'is_featured'     => 'nullable|boolean',

            'thumbnail'       => ['nullable', File::image()->max(5120)],
            'images' => ['nullable', File::image()->max(5120)],

            'gallery.*'       => ['nullable', File::image()->max(5120)],
            'delete_images.*' => ['numeric', 'exists:service_images,id'],
            
            
        ]);

        $data['type'] = $this->mapType($data['type'] ?? null) ?? $service->type;
        $data['slug'] = blank($data['slug'] ?? null) ? null : $data['slug'];
        $data['status'] = (int) ($data['status'] ?? $service->status);
        $data['is_featured'] = $request->boolean('is_featured', $service->is_featured);

        $service->update($data);

        $newMainId = null;
        $maxSort = (int) ($service->images()->max('sort_order') ?? -1);

        // 1. XÓA ẢNH ĐÃ CHECK
        if ($request->filled('delete_images')) {
            $deleted = $service->images()->whereIn('id', $request->delete_images)->get();
            foreach ($deleted as $img) {
                $path = str_replace(rtrim(env('R2_PUBLIC_DOMAIN'),'/') . '/', '', $img->image_url);
                if (Storage::disk('r2')->exists($path)) {
                    Storage::disk('r2')->delete($path);
                }
                $img->delete();
            }

            // Nếu ảnh main bị xóa → sẽ set lại ở dưới
            if ($service->images()->where('is_main', true)->doesntExist()) {
                $newMainId = null; // sẽ lấy ảnh đầu tiên
            }
        }
        foreach ($request->file('images', []) as $file) {
            if (!$file->isValid()) continue;
            $url = $this->storeImageFile($file, $service->service_id, 'main');
            $img = $service->images()->create([
                'image_url'  => $url,
                'sort_order' => ++$maxSort,
                'is_main'    => false,
            ]);
        }

        // 2. UPLOAD ẢNH CHÍNH MỚI (nếu có)
foreach ($request->file('images', []) as $file) {
    if (!$file->isValid()) continue;
    $url = $this->storeImageFile($file, $service->service_id, 'main');
    $img = $service->images()->create([
        'image_url'  => $url,
        'sort_order' => ++$maxSort,
        'is_main'    => false,
    ]);
    if ($newMainId === null) {
        $newMainId = $img->id;
    }
}


        // 3. UPLOAD GALLERY MỚI
        if ($request->hasFile('gallery')) {
            $slotLeft = self::MAX_GALLERY_IMAGES - $service->images()->count();
            $files = array_slice($request->file('gallery'), 0, $slotLeft);

        

            foreach ($files as $file) {
                if (!$file->isValid()) continue;

                $url = $this->storeImageFile($file, $service->service_id, 'gallery');
                $img = $service->images()->create([
                    'image_url'  => $url,
                    'sort_order' => ++$maxSort,
                    'is_main'    => false,
                ]);

                if ($newMainId === null) {
                    $newMainId = $img->id;
                }
            }
        }

        // 4. CẬP NHẬT THUMBNAIL TRONG BẢNG services
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('services', 'r2');
            $service->thumbnail = $path; // chỉ lưu path
        
        }



        // 5. ĐẢM BẢO LUÔN CÓ 1 ẢNH CHÍNH (is_main = true)
        $hasMain = $service->images()->where('is_main', true)->exists();
        if ($newMainId) {
            $service->images()->update(['is_main' => false]);
            ServiceImage::where('id', $newMainId)->update(['is_main' => true]);
        } elseif (!$hasMain) {
            $first = $service->images()->orderBy('sort_order')->first();
            if ($first) $first->update(['is_main' => true]);
        }
            $service->save();


        return redirect()->route('admin.services.index')->with('success', 'Cập nhật dịch vụ thành công!');
    }

    // ================================== DESTROY ==================================
    public function destroy($id)
    {
        $service = Service::with('images')->findOrFail($id);

        // Xóa thumbnail trong bảng services
        if ($service->thumbnail) {
            $path = str_replace(rtrim(env('R2_PUBLIC_DOMAIN'),'/') . '/', '', $service->thumbnail);
            Storage::disk('r2')->delete($path);
        }

        // Xóa tất cả ảnh trong bảng service_images + file trên R2
        foreach ($service->images as $img) {
            $path = str_replace(rtrim(env('R2_PUBLIC_DOMAIN'),'/') . '/', '', $img->image_url);
            Storage::disk('r2')->delete($path);
            $img->delete();
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Xóa dịch vụ thành công!');
    }
          protected function storeImageFile($file,$id,$folder): string
            {

                $path = "services/$id/$folder";
                $filename = uniqid() . "." . $file->getClientOriginalExtension();

                Storage::disk('r2')->put("$path/$filename", file_get_contents($file));

                return "$path/$filename";
            }
}
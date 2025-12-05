<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    // Chuẩn hóa type: nhận 'single','combo','le','goi','lẻ','gói' -> trả 'Lẻ' | 'Gói'
    private function mapType(?string $t): ?string
    {
        if ($t === null) return null;
        $x = mb_strtolower(trim($t), 'UTF-8');
        return match ($x) {
            'single','le','lẻ' => 'Lẻ',
            'combo','goi','gói' => 'Gói',
            default => null,
        };
    }
    private function uploadToR2($file, $folder)
{
    // Upload file lên Cloudflare R2
    $path = Storage::disk('r2')->put($folder, $file);

    // Sinh URL public
    if (env('R2_PUBLIC_DOMAIN')) {
        return env('R2_PUBLIC_DOMAIN') . '/' . $path;
    }

    return Storage::disk('r2')->url($path);
}


    public function index(Request $request)
    {
        $q          = $request->query('q');
        $status     = $request->query('status');       // 0|1
        $categoryId = $request->query('category_id');  // id
        $type       = $request->query('type');         // single|combo|Lẻ|Gói
        $featured   = $request->query('featured');     // 1

        $typeDb = $this->mapType($type); // cho phép cả 2 kiểu tham số

        $services = Service::query()
            ->with('category')
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('service_name', 'like', "%{$q}%")
                      ->orWhere('slug', 'like', "%{$q}%")
                      ->orWhere('short_desc', 'like', "%{$q}%");
                });
            })
            ->when($status !== null && $status !== '', fn($qb) => $qb->where('status', (int)$status))
            ->when($categoryId, fn($qb) => $qb->where('category_id', $categoryId))

            ->when($typeDb, fn($qb) => $qb->where('type', $typeDb))
            ->when($featured, fn($qb) => $qb->where('is_featured', 1))
            ->latest('service_id')
            ->paginate(10)
            ->appends($request->query());

        $categories = ServiceCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('category_name')
            ->get(['category_id','category_name']);

        return view('dashboard.services.index', compact('services', 'categories'));
    }

    public function create()
    {
        $categories = ServiceCategory::whereNotNull('parent_id')->orderBy('category_name')->get();
        return view('dashboard.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_name'   => ['required','string','max:255'],
            'short_desc'     => ['nullable','string','max:255'],
            'category_id'    => ['required','exists:service_categories,category_id'],
            'type'           => ['required', Rule::in(['Lẻ','Gói'])],  // giữ tiếng Việt trong DB
            'slug'           => ['nullable','string','max:255','unique:services,slug'],
            'price'          => ['nullable','numeric','min:0'],
            'price_original' => ['nullable','numeric','min:0'],
            'price_sale'     => ['nullable','numeric','min:0','lte:price_original'],
            'duration'       => ['required','integer','min:1'],
            'description'    => ['nullable','string'],
            'images'         => ['nullable','image','mimes:jpg,jpeg,png,webp,gif','max:5120'],
            'thumbnail'      => ['nullable','image','mimes:jpg,jpeg,png,webp,gif','max:5120'],
            'status'         => ['required','boolean'],
            'is_featured'    => ['nullable','boolean'],
        ]);

        // Chuẩn hóa type (nếu form/JS gửi “single/combo” lạc vào)
        $data['type'] = $this->mapType($data['type']) ?? 'Lẻ';

        // Slug rỗng -> null để tránh duplicate '' trên unique index
        if (array_key_exists('slug', $data) && blank($data['slug'])) {
            $data['slug'] = null;
        }
            if ($request->hasFile('thumbnail')) {
    $url = $this->uploadToR2($request->file('thumbnail'), "services/thumbnails");

    // Lưu URL vào DB
    $data['thumbnail'] = $url;
}

if ($request->hasFile('images')) {
    $url = $this->uploadToR2($request->file('images'), "services/images");

    // Lưu URL vào DB
    $data['images'] = $url;
}



        $data['status']      = $request->boolean('status') ? 1 : 0;
        $data['is_featured'] = $request->boolean('is_featured');
        // $data['effects'] = " ";

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Thêm dịch vụ thành công');
    }

    public function edit($id)
    {
        $service    = Service::findOrFail($id);
        $categories = ServiceCategory::whereNotNull('parent_id')->orderBy('category_name')->get();
        return view('dashboard.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $data = $request->validate([
            'service_name'   => ['required','string','max:255'],
            'short_desc'     => ['nullable','string','max:255'],
            'category_id'    => ['required','exists:service_categories,category_id'],
            'type'           => ['required', Rule::in(['Lẻ','Gói'])],
            'slug'           => ['nullable','string','max:255', Rule::unique('services','slug')->ignore($service->service_id, 'service_id')],
            'price'          => ['nullable','numeric','min:0'],
            'price_original' => ['nullable','numeric','min:0'],
            'price_sale'     => ['nullable','numeric','min:0','lte:price_original'],
            'duration'       => ['required','integer','min:1'],
            'description'    => ['nullable','string'],
            'images'         => ['nullable','image','mimes:jpg,jpeg,png,webp,gif','max:5120'],
            'thumbnail'      => ['nullable','image','mimes:jpg,jpeg,png,webp,gif','max:5120'],
            'status'         => ['required','boolean'],
            'is_featured'    => ['nullable','boolean'],
        ]);

        // Chuẩn hóa type đề phòng form *cũ*
        $data['type'] = $this->mapType($data['type']) ?? $service->type;

        // Slug rỗng -> null
        if (array_key_exists('slug', $data) && blank($data['slug'])) {
            $data['slug'] = null;
        }
if ($request->hasFile('thumbnail')) {

    if ($service->thumbnail) {
        $old = str_replace(env('R2_PUBLIC_DOMAIN').'/', '', $service->thumbnail);
        Storage::disk('r2')->delete($old);
    }

    $data['thumbnail'] = $this->uploadToR2($request->file('thumbnail'), "services/thumbnails");

} else {
    $data['thumbnail'] = $service->thumbnail;
}

if ($request->hasFile('images')) {

    if ($service->images) {
        $old = str_replace(env('R2_PUBLIC_DOMAIN').'/', '', $service->images);
        Storage::disk('r2')->delete($old);
    }

    $data['images'] = $this->uploadToR2($request->file('images'), "services/images");

} else {
    $data['images'] = $service->images;
}


        $data['status']      = $request->boolean('status') ? 1 : 0;
        $data['is_featured'] = $request->boolean('is_featured');
        // $data['effects']='';
        $data = $request->only([
    'service_name',
    'short_desc',
    'category_id',
    'effects',
    'slug',
    'price',
    'price_original',
    'price_sale',
    'duration',
    'description',
    'status',
    'is_active',
    'is_featured',
]);

// GIỮ LẠI TYPE CŨ NẾU KHÔNG CHỌN MỚI
$data['type'] = $request->input('type', $service->type);


        return redirect()->route('admin.services.index')->with('success', 'Cập nhật dịch vụ thành công');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

       if ($service->thumbnail) {
    $old = str_replace(env('R2_PUBLIC_DOMAIN') . '/', '', $service->thumbnail);
    Storage::disk('r2')->delete($old);
}

if ($service->images) {
    $old = str_replace(env('R2_PUBLIC_DOMAIN') . '/', '', $service->images);
    Storage::disk('r2')->delete($old);
}


        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Xóa dịch vụ thành công');
    }
}

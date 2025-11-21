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
            $data['thumbnail'] = $request->file('thumbnail')->store('services', 'public');
        }
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images')->store('services', 'public');
        }

        $data['status']      = $request->boolean('status') ? 1 : 0;
        $data['is_featured'] = $request->boolean('is_featured');

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
            if ($service->thumbnail && Storage::disk('public')->exists($service->thumbnail)) {
                Storage::disk('public')->delete($service->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('services', 'public');
        }
        if ($request->hasFile('images')) {
            if ($service->images && Storage::disk('public')->exists($service->images)) {
                Storage::disk('public')->delete($service->images);
            }
            $data['images'] = $request->file('images')->store('services', 'public');
        }

        $data['status']      = $request->boolean('status') ? 1 : 0;
        $data['is_featured'] = $request->boolean('is_featured');

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Cập nhật dịch vụ thành công');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        if ($service->images && Storage::disk('public')->exists($service->images)) {
            Storage::disk('public')->delete($service->images);
        }
        if ($service->thumbnail && Storage::disk('public')->exists($service->thumbnail)) {
            Storage::disk('public')->delete($service->thumbnail);
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Xóa dịch vụ thành công');
    }
}

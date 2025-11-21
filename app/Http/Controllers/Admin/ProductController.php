<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private const MAX_SUB_IMAGES = 4;
    // Hiển thị danh sách sản phẩm
    public function index(Request $request)
    {
        // 1) Lấy & chuẩn hoá input
        $rawStatus       = $request->query('status');         // '1','2','3' hoặc rỗng
        $rawBrandId      = $request->query('brand_id');       // brand_id hoặc rỗng
        $rawCategoryName = trim((string) $request->query('category_name')); // tên danh mục con hoặc rỗng
      

        // Chỉ nhận status hợp lệ 1/2/3
        $status = in_array($rawStatus, ['1','2','3'], true) ? (int) $rawStatus : null;

        // Chỉ nhận brand_id là số dương
        $brandId = (ctype_digit((string) $rawBrandId) && (int)$rawBrandId > 0) ? (int)$rawBrandId : null;

        // Nếu có nhập category_name thì phải tồn tại trong danh mục CON
        $categoryName = null;
        if ($rawCategoryName !== '') {
            $existsChild = ProductCategory::whereNotNull('parent_id')
                ->where('category_name', $rawCategoryName)
                ->exists();
            if ($existsChild) {
                $categoryName = $rawCategoryName;
            }
        }

        // 2) Query sản phẩm: chỉ áp điều kiện khi biến đã chuẩn hoá != null
        $products = Product::query()
            ->with([
                'brand',
                'category',
                // load 2 ảnh đầu cho UI hover
                'imagesRel' => fn ($q) => $q->orderBy('sort_order'),
            ])
            ->when(!is_null($status),      fn($q) => $q->where('status', $status))
            ->when(!is_null($brandId),     fn($q) => $q->where('brand_id', $brandId))
            ->when(!is_null($categoryName), function ($q) use ($categoryName) {
                $q->whereHas('category', function ($sub) use ($categoryName) {
                    $sub->whereNotNull('parent_id')
                        ->where('category_name', $categoryName);
                });
            })
            ->latest('created_at')
            ->paginate(10)
            ->appends($request->only(['status','brand_id','category_name']));

        // 3) Dữ liệu dropdown
        $brands = Brand::all(['brand_id','brand_name']);
        $categories = ProductCategory::whereNotNull('parent_id')
            ->orderBy('category_name')
            ->get(['category_name']);

        return view('dashboard.products.index', compact('products','brands','categories'));
    }

    // Form thêm sản phẩm
    public function create()
    {
        $brands     = Brand::all();
        $categories = ProductCategory::whereNotNull('parent_id')->get();
        return view('dashboard.products.create' ,compact('categories', 'brands'));
    }

    // Lưu sản phẩm mới
  public function store(Request $request)
{
    $data = $request->validate([
        'product_name'      => 'required|max:255',
        'brand_id'          => 'nullable|integer|exists:brands,brand_id',
        'category_id'       => 'nullable|integer|exists:product_categories,category_id',
        'price'             => 'required|numeric|min:0',
        'discount_percent'  => 'nullable|numeric|min:0|max:100',
        'status'            => 'nullable|string',
        'stock_quantity'    => 'nullable|integer|min:0',
        // 👇 FIX bảng
        'slug'              => ['nullable','string','max:255','unique:products,slug'],
        'images'            => ['sometimes','nullable', File::image()->types(['jpg','jpeg','png','webp'])->max(2*1024)],
        'gallery'           => ['sometimes','array','max:'.self::MAX_SUB_IMAGES],
        'gallery.*'         => ['sometimes','nullable', File::image()->types(['jpg','jpeg','png','webp'])->max(2*1024)],
        'description'       => 'sometimes|nullable|string',
        'capacity'          => 'sometimes|string',
    ]);

    // Tính discount_price
    $data['discount_price'] = $data['price'];
    if (!empty($data['discount_percent'])) {
        $data['discount_price'] = max(0, $data['price'] * (100 - $data['discount_percent']) / 100);
    }

    // Map status text -> number
    $statusMap = ['dang_ban'=>1,'ngung_ban'=>2,'het_hang'=>3];
    if (isset($data['status'], $statusMap[$data['status']])) {
        $data['status'] = $statusMap[$data['status']];
    }

    // Không fill trực tiếp field upload
    unset($data['images'], $data['gallery']);

    $product = Product::create($data);

    // Có thể thay đổi status theo stock -> nhớ save lại
    $product->syncStatusByStock();
    $product->save();

    // ===== Upload ảnh =====
    $maxSort   = -1;
    $newMainId = null;   // 👈 KHỞI TẠO

    // Ảnh chính
    if ($request->hasFile('images')) {
        $url = $this->storeImageFile($request->file('images'), $product->product_id);
        ProductImage::create([
            'product_id' => $product->product_id,
            'url'        => $url,
            'sort_order' => ++$maxSort,
            'is_main'    => true,
        ]);
    }

    // Gallery (giới hạn tối đa MAX_SUB_IMAGES)
    if ($request->hasFile('gallery')) {
        $files = array_slice($request->file('gallery'), 0, self::MAX_SUB_IMAGES);
        foreach ($files as $file) {
            if (!$file) continue;
            $url = $this->storeImageFile($file, $product->product_id);
            $img = ProductImage::create([
                'product_id' => $product->product_id,
                'url'        => $url,
                'sort_order' => ++$maxSort,
                'is_main'    => false,
            ]);
            // Nếu CHƯA có ảnh chính (user không upload images) -> đánh dấu ảnh gallery đầu tiên để lát set main
            if ($newMainId === null && !ProductImage::where('product_id',$product->product_id)->where('is_main',true)->exists()) {
                $newMainId = $img->id;
            }
        }
    }

    // Nếu chưa có ảnh main (không up images), chốt ảnh đầu gallery làm main
    if ($newMainId) {
        ProductImage::where('product_id', $product->product_id)->update(['is_main' => false]);
        ProductImage::where('id', $newMainId)->update(['is_main' => true]);
    } elseif (!ProductImage::where('product_id',$product->product_id)->where('is_main',true)->exists()) {
        // Nếu không upload gì cả mà vẫn có ảnh => set ảnh đầu tiên
        $first = ProductImage::where('product_id',$product->product_id)->orderBy('sort_order')->first();
        if ($first) $first->update(['is_main'=>true]);
    }

    return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
}


    // Hiển thị chi tiết sản phẩm
    public function show($id)
    {
        $brands  = Brand::all();
        $product = Product::with(['imagesRel' => fn($q) => $q->orderBy('sort_order')])->findOrFail($id);
        return view('dashboard.products.show', compact('product', 'brands'));
    }

    // Form sửa sản phẩm
    public function edit($id)
    {
        $categories = ProductCategory::whereNotNull('parent_id')->get();
        $brands     = Brand::all();
        $product    = Product::with(['imagesRel' => fn($q) => $q->orderBy('sort_order')])->findOrFail($id);
        return view('dashboard.products.edit', compact('product','categories', 'brands'));
    }

    // Cập nhật sản phẩm
  public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $product->syncStatusByStock();
    $newMainId = null; 
    $data = $request->validate([
        'product_name'      => 'sometimes|required|max:255',
        'brand_id'          => 'sometimes|nullable|integer|exists:brands,brand_id',
        'category_id'       => 'sometimes|nullable|integer|exists:product_categories,category_id',
        'capacity'       => 'sometimes|string',
        'price'             => 'sometimes|required|numeric|min:0',
        'discount_percent'  => 'sometimes|nullable|numeric|min:0|max:100',
        'stock_quantity'     => 'sometimes|nullable|integer|min:0',
        'status'            => 'sometimes|nullable|string',
        'slug'           => ['nullable','string','max:255','unique:services,slug'],
        // ảnh mới
        'images'            => ['sometimes','nullable', File::image()->types(['jpg','jpeg','png','webp'])->max(2*1024)],
         'gallery'    => ['sometimes','array'],
        'gallery.*'         => ['sometimes','nullable', File::image()->types(['jpg','jpeg','png','webp'])->max(2*1024)],
        'description'       => 'sometimes|nullable|string',
        // danh sách id ảnh phụ tick xoá (không bắt buộc submit)
        'delete_sub_images' => ['sometimes','array'],
        'delete_sub_images.*' => ['integer','exists:product_images,id'],
         'capacities.*' => ['string','max:50'],
    ]);

    // Recalculate discount_price nếu có thay đổi
    if (array_key_exists('price', $data) || array_key_exists('discount_percent', $data)) {
        $price   = $data['price'] ?? $product->price;
        $percent = $data['discount_percent'] ?? $product->discount_percent ?? 0;
        $data['discount_price'] = max(0, $price - ($price * $percent / 100));
    }

    // Map status string -> number nếu cần
    $statusMap = ['dang_ban'=>1,'ngung_ban'=>2,'het_hang'=>3];
    if (isset($data['status']) && isset($statusMap[$data['status']])) {
        $data['status'] = $statusMap[$data['status']];
    }

    // Không ghi đè các field upload lên products
    unset($data['images'], $data['gallery'], $data['delete_sub_images']);

    $product->update($data);

    // ===== XÓA ẢNH PHỤ (trước) =====
if ($request->filled('delete_sub_images') && is_array($request->delete_sub_images)) {
    $ids = array_map('intval', $request->delete_sub_images);

    $toDelete = ProductImage::where('product_id', $product->product_id)
        ->whereIn('id', $ids)
        ->get();

    foreach ($toDelete as $img) {
        Storage::disk('public')->delete(str_replace('/storage/', '', $img->url));
        $img->delete();
    }
}


    // ===== TÍNH LẠI sort_order SAU KHI XOÁ =====
    $maxSort = (int) ($product->imagesRel()->max('sort_order') ?? -1);

    // Sẽ dùng sau để set ảnh chính
    $newMainId = null;

    // ===== UPLOAD ẢNH CHÍNH MỚI (nếu có) =====
    if ($request->hasFile('images')) {
        $url = $this->storeImageFile($request->file('images'), $product->product_id);
        $img = ProductImage::create([
            'product_id' => $product->product_id,
            'url'        => $url,
            'sort_order' => ++$maxSort,
            'is_main'    => false, // set ở dưới
        ]);
        $newMainId = $img->id;
    }

    // ===== UPLOAD NHIỀU ẢNH PHỤ (gallery[]) =====
    if ($request->hasFile('gallery')) {
        foreach ($request->file('gallery') as $file) {
            if (!$file) continue;
            $url = $this->storeImageFile($file, $product->product_id);
            ProductImage::create([
                'product_id' => $product->product_id,
                'url'        => $url,
                'sort_order' => ++$maxSort,
                'is_main'    => false,
            ]);
        }
    }

    // ===== CHỐT ẢNH CHÍNH =====
    // Nếu hiện tại không còn ảnh nào là main (VD: đã xoá), hoặc có upload ảnh chính mới, thì set lại.
    $hasMain = ProductImage::where('product_id', $product->product_id)->where('is_main', true)->exists();

    if ($newMainId) {
        // Có ảnh chính mới được upload → set nó làm main
        ProductImage::where('product_id', $product->product_id)->update(['is_main' => false]);
        ProductImage::where('id', $newMainId)->update(['is_main' => true]);
    } elseif (!$hasMain) {
        // Không có ảnh main nào (có thể do xoá main) → set ảnh đầu tiên còn lại làm main
        $first = $product->imagesRel()->first();
        if ($first) { $first->update(['is_main' => true]); }
    }

    return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
}

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        // Nhờ FK onDelete('cascade') để xoá ảnh kèm theo
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    // ===== Helper lưu file ảnh vào disk public và trả URL tuyệt đối =====
    protected function storeImageFile($file, $productId): string
    {
        // Lưu vào storage/app/public/uploads/products/{product_id}
        $path = $file->store("uploads/products/{$productId}", 'public');
        return Storage::url($path); // ví dụ /storage/uploads/products/1/abc.jpg
    }
}

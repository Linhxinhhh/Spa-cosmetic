<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductUserController extends Controller
{
    /** Trang tất cả sản phẩm */
    public function index(Request $request)
    {
        $products = $this->buildProductQuery($request)
            ->paginate(12)
            ->appends($request->query());

        // Featured
        $featuredProducts = Product::where('is_featured', 1)->take(6)->get();
        $productLeft  = $featuredProducts[0] ?? null;
        $productRight = $featuredProducts[1] ?? null;

        // Sidebar: danh mục cha + con + đếm
        $parentCategories = ProductCategory::query()
            ->whereNull('parent_id')
            ->withCount('products')
            ->with(['children' => fn ($q) => $q->withCount('products')->orderBy('category_name')])
            ->orderBy('category_name')
            ->get();

        $brands = Brand::withCount('products')->orderBy('brand_name')->get();

        // Hot products
        $hotProducts = Product::with('category')
            ->when(
                Product::where('is_featured', 1)->exists(),
                fn ($q) => $q->where('is_featured', 1)->orderByDesc('updated_at'),
                fn ($q) => $q->orderByDesc('sold_quantity')
            )
            ->take(3)->get();

        $sidebarBanner = Banner::query()
            ->where('status', 1)
            ->where('position', 'sidebar')
            ->latest('updated_at')
            ->first();
            $sort  = $request->input('sort');                 // featured|newest|sold|rating|price_asc|price_desc
    $query = $this->buildProductQuery($request);

    // Giá thực tế để sort (ưu tiên discount_price nếu có)
    $priceExpr = DB::raw('COALESCE(products.discount_price, products.price)');

    // Áp sắp xếp
    $query = match ($sort) {
        'featured'   => $query->orderByDesc('is_featured')->orderByDesc('updated_at'),
        'newest'     => $query->latest('product_id'),               // hoặc created_at
        'sold'       => $query->orderByDesc('sold_quantity'),
        'rating'     => $query->orderByDesc('avg_rating')           // nếu có cột avg_rating
                                 ->orderByDesc('reviews_count'),    // nếu có cột reviews_count
        'price_asc'  => $query->orderBy($priceExpr, 'asc'),
        'price_desc' => $query->orderBy($priceExpr, 'desc'),
        default      => $query->latest('product_id'),
    };
        return view(
            'Users.products.index',
            compact('products', 'featuredProducts', 'productLeft', 'productRight', 'brands', 'sidebarBanner', 'hotProducts', 'parentCategories')
        );
    }


public function byCategory(ProductCategory $category, Request $request)
{
    // Lọc sản phẩm theo danh mục hiện tại
    $request->merge(['category_id' => $category->category_id]);

    // Eager load: cha + children (kèm count sản phẩm)
    $category->load([
        'parent',
        'children' => fn ($q) => $q->withCount('products')
                                   ->orderBy('category_name'),
    ]);
    $children = $category->children; // <-- children của danh mục hiện tại

    // Sản phẩm
    $products = $this->buildProductQuery($request)
        ->paginate(12)
        ->appends($request->query());

    // Danh mục cha cấp 1 (menu bên)
    $parentCategories = ProductCategory::whereNull('parent_id')
        ->withCount('products')
        ->with(['children' => fn ($q) => $q->withCount('products')
                                           ->orderBy('category_name')])
        ->orderBy('category_name')
        ->get();

    // Thương hiệu
    $brands = Brand::withCount('products')->orderBy('brand_name')->get();

    return view('Users.products.by_category', compact(
        'category', 'children', 'products', 'parentCategories', 'brands'
    ));
}


    /** Trang theo thương hiệu */


public function byBrand(Brand $brand, Request $request)
{
    $request->merge(['brand_id' => $brand->brand_id]); // hoặc $brand->getKey()
    $products = $this->buildProductQuery($request)
        ->paginate(12)
        ->appends($request->query());

    return view('Users.products.by-brand', compact('brand', 'products'));
}


    /** Chi tiết sản phẩm */
    public function show(Product $product)
    {
        $product->load([
            'imagesRel' => fn ($q) => $q->orderBy('sort_order'),
            'category.parent',
        ]);

        // Biến thể theo dung tích: cùng category_id + tên có tiền tố tương tự
        $prefix = Str::of($product->product_name)->words(5, '');
        $variants = Product::query()
            ->select('product_id', 'slug', 'capacity', 'category_id', 'product_name')
            ->where('status', 1)
            ->where('category_id', $product->category_id)
            ->whereNotNull('capacity')->where('capacity', '!=', '')
            ->where(function ($q) use ($prefix) {
                $q->where('product_name', 'like', trim($prefix) . '%');
            })
            ->orderByRaw("CAST(REGEXP_REPLACE(capacity, '[^0-9]', '') AS UNSIGNED) ASC")
            ->get()
            ->unique('capacity')
            ->values();

        // Fallback nếu sản phẩm chỉ có một dung tích
        if ($variants->isEmpty() && !empty($product->capacity)) {
            $variants = Product::where('product_id', $product->product_id)
                ->get(['product_id', 'slug', 'capacity']);
        }

        // Sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Product::where('status', 1)
            ->where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->latest('product_id')->take(8)->get();

        // Cờ wishlist cho UI (nếu view cần)
        $isInWishlist = auth()->check()
            ? auth()->user()->wishlist()->wherePivot('product_id', $product->product_id)->exists()
            : false;

        return view('Users.products.show', compact('product', 'variants', 'relatedProducts', 'isInWishlist'));
    }

    /** Tìm kiếm */
    public function search(Request $request)
    {
        $items = $this->buildProductQuery($request)
            ->paginate(12)
            ->appends($request->query());

        $categories = ProductCategory::whereNotNull('parent_id')
            ->orderBy('category_name')
            ->get(['category_id', 'category_name']);

        $brands = Brand::orderBy('brand_name')->get(['brand_id', 'brand_name']);

        $q = trim($request->query('q', ''));

        return view('Users.products.search', compact('items', 'categories', 'q', 'brands'));
    }

    /** Core query dùng chung */
    private function buildProductQuery(Request $r)
    {
        $q        = trim($r->query('q', ''));
        $catId    = $r->query('category_id');
        $brandId  = $r->query('brand_id');
        $type     = $r->query('type');
        $featured = $r->boolean('featured');
        $sort     = $r->query('sort', 'relevant');

        $min = $r->filled('price_min') ? (int) $r->price_min : null;
        $max = $r->filled('price_max') ? (int) $r->price_max : null;

        // Giá hiển thị: ưu tiên discount_price > 0
        $priceExpr = DB::raw('COALESCE(NULLIF(discount_price,0), NULLIF(price,0), price)');

        $query = Product::query()
            ->with(['imagesRel' => fn ($q) => $q
                ->orderByDesc('is_main')->orderBy('sort_order')->orderBy('id')
            , 'brand', 'category'])
            ->where('status', 1)
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('product_name', 'like', "%{$q}%")
                      ->orWhere('short_desc', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
                });
            })
            // Lọc category: lấy luôn tất cả danh mục con của category_id (đệ quy)
            ->when($catId, function ($qb) use ($catId) {
                $ids = $this->descendantCategoryIds((int) $catId);
                $qb->whereIn('category_id', $ids);
            })
            ->when($brandId, fn ($qb) => $qb->where('brand_id', $brandId))
            ->when($type, fn ($qb) => $qb->where('type', $type))
            ->when($featured, fn ($qb) => $qb->where('is_featured', 1))
            ->when($min !== null && $max !== null, fn ($qb) =>
                $qb->whereBetween($priceExpr, [$min, $max])
            )
            ->when($min !== null && $max === null, fn ($qb) =>
                $qb->whereRaw('( ' . $priceExpr . ' ) >= ?', [$min])
            )
            ->when($min === null && $max !== null, fn ($qb) =>
                $qb->whereRaw('( ' . $priceExpr . ' ) <= ?', [$max])
            );

 return match ($sort) {
    'price_asc'  => $query->orderBy($priceExpr, 'asc'),
    'price_desc' => $query->orderBy($priceExpr, 'desc'),
    'newest'     => $query->latest('product_id'),
    'popular'    => $query->orderByDesc('sold_quantity'),
    default      => $query->latest('product_id'),
};

    }

    /** Lấy id của category + tất cả con (đệ quy) */
    private function descendantCategoryIds(int $rootId): array
    {
        $all = ProductCategory::select('category_id', 'parent_id')->get()->groupBy('parent_id');
        $ids = [$rootId];

        $stack = [$rootId];
        while ($stack) {
            $parent = array_pop($stack);
            foreach ($all[$parent] ?? [] as $child) {
                $ids[] = $child->category_id;
                $stack[] = $child->category_id;
            }
        }
        return array_values(array_unique($ids));
    }
}

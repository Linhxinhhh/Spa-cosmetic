<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ProductCategory;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
public function __construct()
{
    $this->middleware('auth')->except(['index']);
}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
public function index()
    {
        
         $brands = Brand::query()
        ->select('brand_id','brand_name','slug','logo')   // tùy cột bạn đang dùng
        ->withCount('products')                        // cần quan hệ Brand->products
        ->orderBy('brand_name')
        ->get();
        // Lấy banner ở vị trí homepage_top, đang bật
        $bannersTop = Banner::position('homepage_top')
            ->active()
            ->orderByDesc('created_at')
            ->get(['banner_id','title','image','link']);

        // ❗ Nếu bạn dùng view Users/home.blade.php (chữ U hoa), nhớ đúng đường dẫn/case
        $productsAll = Product::active()->latest('product_id')->take(8)->get();

    // Hàng mới về (trong 30 ngày)
    $productsNew = Product::active()
        ->where('created_at', '>=', now()->subDays(30))
        ->latest('created_at')->take(8)->get();

    // Nổi bật: lấy theo % giảm cao (vì DB không có cờ featured)
    $productsFeatured = Product::active()
        ->whereNotNull('discount_percent')->where('discount_percent', '>', 0)
        ->orderByDesc('discount_percent')->take(8)->get();

    // Bán chạy: theo sold_quantity
    $productsBest = Product::active()
        ->orderByDesc('sold_quantity')->take(8)->get();
        // san pham noi bat banner
          $featuredProducts = \App\Models\Product::where('is_featured', 1)->take(6)->get();
    $productLeft = $featuredProducts->first();
    $productRight = $featuredProducts->skip(1)->first();
        //dich vu noi bat
            $featuredServices = Service::query()
        ->featured()
        ->with('category:category_id,category_name')
        ->latest('service_id')
        ->take(8)
        ->get();

    // Phân trang theo DANH MỤC: 1 danh mục / trang
    $categoryPager = ServiceCategory::query()
        ->whereNotNull('parent_id')
        ->whereHas('services', fn($q) => $q->active())     // chỉ danh mục có dịch vụ active
        ->orderBy('category_name')
                              // giữ các query string khác
            ->paginate(1, ['*'], 'cat_page')              // paginate trước
            ->appends(request()->query());

    // Lấy danh mục hiện tại (trang hiện tại của paginator)
    $currentCategory = $categoryPager->first(); // vì perPage=1

    // Phân trang theo DỊCH VỤ thuộc danh mục hiện tại (nếu có)
    $servicesPager = null;
    if ($currentCategory) {
        $servicesPager = Service::query()
            ->active()
            ->where('category_id', $currentCategory->category_id)
            ->orderBy('type')
            ->orderBy('service_name')
            ->paginate(8, ['*'], 'sv_page')               // tham số trang riêng cho dịch vụ
            ->appends(['cat_page' => $categoryPager->currentPage()]); // giữ trang danh mục
    }

             $priceCategories = ServiceCategory::query()
            ->whereHas('services', function ($q) {
                $q->where('status', 1);
            })
            ->with(['services' => function ($q) {
                $q->where('status', 1)
                  ->orderBy('type')        // Lẻ/Gói
                  ->orderBy('service_name');
            }])
            ->orderBy('category_name')
            ->get();
               // Chỉ lấy danh mục con, đang active
        $hotCategories = ProductCategory::childrenOnly()
            ->active()
            ->with('parent')               // nếu cần hiển thị tên danh mục cha
            ->orderBy('category_name')
            ->take(18)                     // số lượng bạn muốn hiển thị
            ->get();

            $bestSellers = Product::query()
        ->with(['category'])                  // nếu cần
        ->withCount('orderItems')             // quan hệ hasMany OrderItem
        ->orderByDesc('order_items_count')
        ->take(6)
        ->get();
        return view('Users.home', ['bannersTop' => $bannersTop],compact('productsAll','productsNew','productsFeatured','productsBest','brands','featuredServices',  'categoryPager',
        'currentCategory',
        'servicesPager','priceCategories','featuredProducts','productLeft','productRight','hotCategories','bestSellers'));
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('Users.products.show', compact('product, brands '));
    }

}



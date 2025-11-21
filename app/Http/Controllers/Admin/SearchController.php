<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ServiceCategory;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Service;
use App\Models\Banner;


class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->get('keyword', ''));

        $results = ProductCategory::query()
            ->with('children')             // load tất cả con để hiển thị dưới cha
            ->whereNull('parent_id')       // chỉ lấy danh mục cấp 1
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($w) use ($keyword) {
                    // Cha khớp
                    $w->where('category_name', 'like', "%{$keyword}%")
                      ->orWhere('slug', 'like', "%{$keyword}%")
                    // Hoặc có con khớp
                      ->orWhereHas('children', function ($cw) use ($keyword) {
                          $cw->where('category_name', 'like', "%{$keyword}%")
                             ->orWhere('slug', 'like', "%{$keyword}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['keyword' => $keyword]);

        return view('dashboard.categories.products.search', [
            'results' => $results,
            'keyword' => $keyword,
        ]);
    }
    public function search(Request $request)
    {
        $keyword = trim($request->get('keyword', ''));

        $results = ServiceCategory::query()
            ->with('children')             // load tất cả con để hiển thị dưới cha
            ->whereNull('parent_id')       // chỉ lấy danh mục cấp 1
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($w) use ($keyword) {
                    // Cha khớp
                    $w->where('category_name', 'like', "%{$keyword}%")
                      ->orWhere('slug', 'like', "%{$keyword}%")
                    // Hoặc có con khớp
                      ->orWhereHas('children', function ($cw) use ($keyword) {
                          $cw->where('category_name', 'like', "%{$keyword}%")
                             ->orWhere('slug', 'like', "%{$keyword}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['keyword' => $keyword]);

        return view('dashboard.categories.service.search', [
            'results' => $results,
            'keyword' => $keyword,
        ]);
    }
    public function products(Request $request)
    {
        $keyword      = trim($request->get('keyword', ''));
        $brandId      = $request->get('brand_id');
        $categoryId   = $request->get('category_id');

        $results = Product::query()
            ->with(['brand', 'category'])
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where('product_name', 'like', "%{$keyword}%");
            })
            ->when($brandId, function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->only(['keyword', 'brand_id', 'category_id']));

        $brands     = Brand::all();
        $categories = ProductCategory::all();

        return view('dashboard.products.search', [
            'results'    => $results,
            'keyword'    => $keyword,
            'brands'     => $brands,
            'categories' => $categories,
            'brandId'    => $brandId,
            'categoryId' => $categoryId,
            

        ]);
    }
    public function services(Request $request)
    {
        $keyword      = trim($request->get('keyword', ''));
      
        $categoryId   = $request->get('category_id');

        $results = Service::query()
            ->with([ 'category'])
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where('service_name', 'like', "%{$keyword}%");
            })
            
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->only(['keyword',  'category_id']));

      
        $categories = ServiceCategory::all();

        return view('dashboard.services.search', [
            'results'    => $results,
            'keyword'    => $keyword,
      
            'categories' => $categories,
            'categoryId' => $categoryId,
            

        ]);
    }
    public function brands(Request $request)
    {
        $keyword = trim($request->get('keyword', ''));

        $results = Brand::query()
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where('brand_name', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['keyword' => $keyword]);

        return view('dashboard.brands.search', [
            'results' => $results,
            'keyword' => $keyword,
        ]);
    }
        public function banner(Request $request)
    {
        $keyword = trim($request->get('keyword', ''));

        $results = Banner::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('link', 'like', "%{$keyword}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('dashboard.banners.search', compact('results', 'keyword'));
    }



}

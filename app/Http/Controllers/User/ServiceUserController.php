<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Guide;
class ServiceUserController extends Controller
{
    public function index(Request $req)
    {
        $q       = trim((string) $req->input('q'));
        $catId   = $req->input('category_id'); // có thể là string (VD: SV014)
        $min     = $req->input('min_price');
        $max     = $req->input('max_price');
        $sort    = $req->input('sort', 'new');
        $perPage = (int) $req->input('per_page', 42);
        $parentId= $req->input('parent_id');

        // 1) Xây biểu thức giá hiệu lực
        $priceExpr = "CASE
            WHEN price_sale IS NOT NULL AND price_sale > 0 THEN price_sale
            WHEN price IS NOT NULL AND price > 0 THEN price
            ELSE price_original
        END";

        // 2) Base query
        $query = Service::query()
            ->with(['category:category_id,category_name'])
            ->select('services.*')
            ->selectRaw("$priceExpr AS effective_price")
            ->where('is_active', 1)
            ->when($q !== '', fn($q2) => $q2->where('service_name', 'like', "%$q%"));

        // 3) Lọc theo danh mục (kèm danh mục con nếu chọn danh mục cha)
        if (!empty($catId)) {
            // Lấy tất cả id con + chính nó
            $ids = ServiceCategory::where('parent_id', $catId)->pluck('category_id')->all();
            $ids[] = $catId;
            $query->whereIn('category_id', $ids);
        }

        // 4) Lọc theo giá
        if ($min !== null && $min !== '') {
            $query->whereRaw("$priceExpr >= ?", [(int)$min]);
        }
        if ($max !== null && $max !== '') {
            $query->whereRaw("$priceExpr <= ?", [(int)$max]);
        }

        // 5) Sắp xếp
        switch ($sort) {
            case 'popular':
                $query->orderByDesc('is_featured')->orderByDesc('service_id');
                break;
            case 'price_asc':
                // dùng effective_price đã tính
                $query->orderBy('effective_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('effective_price', 'desc');
                break;
            default: // new
                $query->orderByRaw('IFNULL(created_at, "1970-01-01 00:00:00") DESC')
                      ->orderByDesc('service_id');
        }

        $services = $query->paginate($perPage)->appends($req->query());

        // 6) Sidebar danh mục cha
        // Nếu bạn KHÔNG có scope parents() trên model, dùng whereNull('parent_id')
        $serviceParents = ServiceCategory::whereNull('parent_id')
            ->orderBy('category_name', 'asc')
            ->get(['category_id','category_name','slug']);

        // 7) Danh sách danh mục con (nếu cần cho filter)
        $categories = ServiceCategory::query()
            ->when($parentId, fn($q) => $q->where('parent_id', $parentId))
            ->when(!$parentId, fn($q) => $q->whereNotNull('parent_id'))
            ->orderBy('category_name')
            ->get(['category_id','category_name']);

        // 8) Breadcrumb/nhãn danh mục hiện tại
        $currentCat = null;
        if (!empty($catId)) {
            $currentCat = ServiceCategory::where('category_id', $catId)->first();
        }

          $guideBase = Guide::query()
            ->where('status', 1)
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where(function ($x) use ($q) {
                    $x->where('title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%");
                });
            });

        // Hero (bài mới nhất) + 2 bài kế
        $hero = $guideBase->cloneWithout(['orders', 'columns'])
            ->latest('published_at')
            ->latest('created_at')
            ->first(['guide_id','slug','title','thumbnail']);

        $heroSides = $guideBase->cloneWithout(['orders', 'columns'])
            ->latest('published_at')
            ->latest('created_at')
            ->skip(1)->take(2)
            ->get(['guide_id','slug','title','thumbnail']);

        // Phổ biến
        $popularGuides = Guide::where('status', 1)
            ->orderByDesc('views')
            ->limit(6)
            ->get(['guide_id','slug','title','thumbnail','published_at']);

        // Chuẩn hoá URL ảnh cho hero/heroSides/popularGuides
        $toUrl = function (?string $p) {
            if (!$p) return asset('images/placeholder-16x9.jpg');
            return Str::startsWith($p, ['http://','https://','/']) ? $p : asset('storage/'.$p);
        };
        if ($hero)        { $hero->thumb_url = $toUrl($hero->thumbnail); }
        $heroSides->transform(function($g) use ($toUrl) { $g->thumb_url = $toUrl($g->thumbnail); return $g; });
        $popularGuides->transform(function($g) use ($toUrl) { $g->thumb_url = $toUrl($g->thumbnail); return $g; });

        return view('Users.services.index', compact(
            'services','categories','sort','perPage','q','catId','min','max','serviceParents','parentId','currentCat', 'hero','heroSides','popularGuides',
        ));
    }

    public function show(Service $service)
    {
        $related = Service::where('is_active', 1)
            ->where('category_id', $service->category_id)
            ->where('service_id', '!=', $service->service_id)
            ->limit(8)->get();

        return view('Users.services.show', compact('service','related'));
    }

    // /users/dich-vu/danh-muc/{slug}
public function byCategory(Request $request, ServiceCategory $category)
{
    // 0) Xác định "cha gốc" (nếu click vào con -> lấy cha của nó)
    $root = $category->parent_id
        ? ServiceCategory::where('category_id', $category->parent_id)->first()
        : $category;

    // 1) Gom ID của cha + toàn bộ con trực tiếp
    $ids = ServiceCategory::where('parent_id', $root->category_id)
            ->pluck('category_id')->all();
    $ids[] = $root->category_id;

    // --- filters ---
    $q       = trim((string) $request->input('q'));
    $min     = $request->input('min_price');
    $max     = $request->input('max_price');
    $sort    = $request->input('sort', 'new');
    $perPage = (int) $request->input('per_page', 42);

    $priceExpr = "CASE
        WHEN price_sale IS NOT NULL AND price_sale > 0 THEN price_sale
        WHEN price      IS NOT NULL AND price      > 0 THEN price
        ELSE price_original
    END";

    // 2) Build query dịch vụ theo CHA + CON
    $query = Service::query()
        ->with(['category:category_id,category_name'])
        ->select('services.*')
        ->selectRaw("$priceExpr AS effective_price")
        ->where('is_active', 1)
        ->whereIn('category_id', $ids)
        ->when($q !== '', fn($q2) => $q2->where('service_name','like',"%$q%"));

    if ($min !== null && $min !== '') $query->whereRaw("$priceExpr >= ?", [(int)$min]);
    if ($max !== null && $max !== '') $query->whereRaw("$priceExpr <= ?", [(int)$max]);

    switch ($sort) {
        case 'popular':
            $query->orderByDesc('is_featured')->orderByDesc('service_id'); break;
        case 'price_asc':
            $query->orderBy('effective_price', 'asc'); break;
        case 'price_desc':
            $query->orderBy('effective_price', 'desc'); break;
        default:
            $query->orderByRaw('IFNULL(created_at,"1970-01-01 00:00:00") DESC')
                  ->orderByDesc('service_id');
    }

    $services = $query->paginate($perPage)->appends($request->query());

    // 3) Sidebar: danh mục CHA (để list bên trái)
    $serviceParents = ServiceCategory::whereNull('parent_id')
        ->orderBy('category_name')
        ->get(['category_id','category_name','slug']);

    // 4) Nhánh con của CHA (hiển thị "Nhánh con:")
    $children = ServiceCategory::where('parent_id', $root->category_id)
        ->orderBy('category_name')
        ->get(['category_id','category_name','slug']);

    // 5) Truyền "root" ra view dưới tên $category để tiêu đề luôn là danh mục cha
    //    Nếu cần biết người dùng click từ con nào để highlight, truyền thêm $clicked = $category
    $clicked = $category;

    return view('Users.services.byCategory', compact(
        'category',     // chính là $root
        'children',
        'serviceParents',
        'services',
        'q','min','max','sort','perPage',
        'clicked'
    ))->with('category', $root);
}

}

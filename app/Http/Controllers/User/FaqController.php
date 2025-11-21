<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Guide;
use App\Models\ServiceCategory;
use App\Models\Service;
use Illuminate\Http\Request;

class FaqController extends Controller
{
   public function index(Request $request)
    {
        $q   = trim((string) $request->q);
        $sub = $request->subcategory;  // chỉ lọc theo subcategory

        $items = Faq::query()
            ->with(['contact','user'])
            ->where('status', 'Xuất bản')

            // Lọc theo danh mục con
            ->when($sub !== null && $sub !== '', fn($qr) => $qr->where('subcategory', $sub))

            // Tìm kiếm: question/answer/subcategory + contact.message
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($x) use ($q) {
                    $x->where('question',     'like', "%{$q}%")
                      ->orWhere('answer',      'like', "%{$q}%")
                      ->orWhere('subcategory', 'like', "%{$q}%")
                      ->orWhereHas('contact', fn($c) => $c->where('message', 'like', "%{$q}%"));
                });
            })

            // Sắp xếp đẹp
            ->orderByRaw("CASE WHEN category IS NULL OR category = '' THEN 1 ELSE 0 END")
            ->orderBy('category')
            ->orderBy('subcategory')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();
        // Vẫn nhóm theo category để header hiển thị gọn
        $serviceParents = ServiceCategory::query()
            ->whereNull('parent_id')
            ->orderBy('category_name', 'asc')
            ->get(['category_id','category_name','slug']);
         $hotServices = Service::query()
                ->where('is_featured', 1)
                ->orderByDesc('orders_count')
                ->limit(4)
                ->get(['service_id', 'service_name as name', 'slug', 'thumbnail', 'price']);
        $faqs = $items->groupBy(fn($f) => $f->category ?: 'Khác');
            $topPosts = Guide::query()
            ->where('status', '1')           // hoặc scopePublished()
            
            ->orderByDesc('views')                  // nếu có cột views
            ->latest('published_at')                // nếu có cột published_at
            ->limit(6)
            ->get(['guide_id','title','slug','thumbnail']);   // cover nếu có

        // Danh sách subcategory cho dropdown
        $subcategories = Faq::where('status','1')
            ->distinct()->pluck('subcategory')->filter()->values();

        return view('users.faq.index', compact('faqs','q','sub','subcategories','serviceParents','hotServices','topPosts'));

    }
}

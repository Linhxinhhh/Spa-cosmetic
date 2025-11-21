<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Guide, GuideCategory, GuideTag, ServiceCategory, Service, Faq};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuideUserController extends Controller
{
    public function index(Request $r)
    {
        $q   = trim((string) $r->q);
        $per = max(6, min((int) $r->input('per_page', 12), 48));

        $category = $r->filled('category') ? (int) $r->category : null;
        $tag      = $r->filled('tag') ? (int) $r->tag : null;

        // ===== CẨM NANG =====
        $guides = Guide::query()
            ->with(['category:category_id,name','tags:tag_id,name'])
            ->where('status', 1)
            ->when($q, fn($qur) => $qur->where(fn($x) => $x
                ->where('title', 'like', "%{$q}%")
                ->orWhere('excerpt', 'like', "%{$q}%")))
            ->when($category, fn ($qur) => $qur->where('category_id', $category))
            ->when($tag, fn ($qur) => $qur->whereHas('tags', fn ($t) => $t->where('guide_tags.tag_id', $tag)))
            ->latest('published_at')->latest('created_at')
            ->paginate(9)
            ->withQueryString();

        // ===== HỎI ĐÁP: chỉ ảnh bìa + tiêu đề =====
        $items = Faq::query()
            ->with('contact:contact_id,message')
            ->where('status', 'Xuất bản')
            ->when($q, function ($qr) use ($q) {
                $qr->where(fn($x) => $x
                    ->where('question','like',"%{$q}%")
                    ->orWhere('answer','like',"%{$q}%")
                    ->orWhere('subcategory','like',"%{$q}%")
                    ->orWhereHas('contact', fn($c) => $c->where('message','like',"%{$q}%")));
            })
            ->orderByDesc('id')
            ->paginate($per)
            ->withQueryString();

        // Chuẩn hoá field hiển thị
        $items->getCollection()->transform(function ($f) {
            $title = trim((string) ($f->contact->message ?? ''));
            if ($title === '') { $title = trim((string) ($f->question ?? 'Câu hỏi')); }
            $f->title = $title;

            $p = $f->cover_image;
            $f->cover_url = $p
                ? (Str::startsWith($p, ['http://','https://']) ? $p : asset('storage/'.$p))
                : asset('images/placeholder-16x9.jpg');

            return $f;
        });

        return view('Users.guides.index', [
            'q'            => $q,
            'category'     => $category,
            'tag'          => $tag,
            'guides'       => $guides,
            'categories'   => GuideCategory::orderBy('name')->get(['category_id','name']),
            'tags'         => GuideTag::orderBy('name')->get(['tag_id','name']),

            'popularGuides' => Guide::where('status', 1)
                ->orderByDesc('views')
                ->limit(6)
                ->get(['guide_id','slug','title','thumbnail','published_at']),

            'hero' => Guide::where('status', 1)
                ->latest('published_at')
                ->first(['guide_id','slug','title','thumbnail']),

            'heroSides' => Guide::where('status', 1)
                ->latest('published_at')
                ->skip(1)->take(2)
                ->get(['guide_id','slug','title','thumbnail']),

            'serviceParents' => ServiceCategory::whereNull('parent_id')
                ->orderBy('category_name','asc')
                ->get(['category_id','category_name','slug']),

            'hotServices' => Service::where('is_featured', 1)
                ->orderByDesc('orders_count')
                ->limit(4)
                ->get(['service_id','service_name as name','slug','thumbnail','price']),

            // block Hỏi đáp
            'items' => $items,
        ]);
    }

    public function show(Guide $guide, Request $r)
    {
        $guide = Guide::with(['category:category_id,name', 'tags:tag_id,name'])
            ->select(['guide_id','title','slug','thumbnail','excerpt','content_html','category_id','published_at','views','status'])
            ->where('slug', $guide->slug)
            ->firstOrFail();

        $guide->increment('views');

        $related = Guide::query()
            ->where('status', 1)
            ->where('guide_id', '!=', $guide->getKey())
            ->where('category_id', $guide->category_id)
            ->latest('published_at')
            ->take(6)
            ->get(['guide_id','title','slug','thumbnail','published_at']);

        return view('Users.guides.show', [
            'guide'         => $guide,
            'related'       => $related,
            'serviceParents'=> ServiceCategory::whereNull('parent_id')->orderBy('category_name','asc')->get(['category_id','category_name','slug']),
            'hotServices'   => Service::where('is_featured', 1)->orderByDesc('orders_count')->limit(4)->get(['service_id','service_name as name','slug','thumbnail','price']),
            'popularGuides' => Guide::where('status', 1)->orderByDesc('views')->limit(6)->get(['guide_id','slug','title','thumbnail','published_at']),
        ]);
    }
}

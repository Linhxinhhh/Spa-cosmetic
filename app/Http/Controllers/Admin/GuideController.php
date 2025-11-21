<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuideRequest;
use App\Http\Requests\UpdateGuideRequest;
use App\Models\Faq;
use App\Models\{Guide, GuideCategory, GuideTag};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};

class GuideController extends Controller
{
    public function index(Request $r)
    {
        $guides = Guide::query()
            ->with([
                // FIX: eager load đúng cột (tránh N+1)
                'category:category_id,name', // chọn đúng PK + cột cần dùng
    'tags:tag_id,name',
            ])
            // FIX: lọc status đúng cả giá trị "0"
            ->when($r->has('status') && $r->status !== '', fn ($q) =>
                $q->where('status', (int) $r->status)
            )
            ->search($r->q)
            ->latest('published_at')->latest('created_at')
            ->paginate(12)->withQueryString();
            $items = Faq::query()->groupBy(fn($f) => $f->category ?: 'Khác');
            $topPosts = Guide::query()
            ->where('status', '1')           // hoặc scopePublished()
            
            ->orderByDesc('views')                  // nếu có cột views
            ->latest('published_at')                // nếu có cột published_at
            ->limit(6)
            ->get(['guide_id','title','slug']);   // cover nếu có

        return view('dashboard.guides_admin.index', compact('guides','topPosts','items'));
    }

    public function create()
    {
        return view('dashboard.guides_admin.create', [
            'categories' => GuideCategory::orderBy('name')->get(['category_id','name']),
            'tags'       => GuideTag::orderBy('name')->get(['tag_id','name']),
        ]);
       

    }

    public function store(StoreGuideRequest $req)
    {
        $data = $req->validated();
        $data['slug'] = Guide::uniqueSlug($data['title']);

        if ($req->hasFile('thumbnail')) {
            $data['thumbnail'] = $req->file('thumbnail')->store('guides/covers', 'public');
        }
        if ((int)($data['status'] ?? 0) === 1 && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        // FIX: bọc transaction để tạo + gắn tag là 1 đơn vị
        $guide = DB::transaction(function () use ($data, $req) {
            $guide = Guide::create($data);

            $tagIds = collect($req->input('tags', []))
                ->filter()                  // loại null/'' nếu có
                ->map(fn ($v) => (int) $v)  // ép kiểu int
                ->all();

            $guide->tags()->sync($tagIds);
            return $guide;
        });

       return redirect()->route('admin.guides.index', ['guide' => $guide]);
    }

    public function edit(Guide $guide)
    {
        return view('dashboard.guides_admin.edit', [
            // FIX: load cả category để view dùng $guide->category->name
            'guide' => $guide->load([
    'category:category_id,name', // chọn đúng PK + cột cần dùng
    'tags:tag_id,name',
]),
            'categories' => GuideCategory::orderBy('name')->get(['category_id','name']),
            'tags'       => GuideTag::orderBy('name')->get(['tag_id','name']),
        ]);
    }

    public function update(UpdateGuideRequest $req, Guide $guide)
    {
        $data = $req->validated();

        if (isset($data['title']) && empty($data['slug'])) {
            $data['slug'] = Guide::uniqueSlug($data['title']);
        }

        if ($req->hasFile('thumbnail')) {
            if ($guide->thumbnail) {
                Storage::disk('public')->delete($guide->thumbnail);
            }
            $data['thumbnail'] = $req->file('thumbnail')->store('guides/covers', 'public');
        }

        if (array_key_exists('status', $data) && (int)$data['status'] === 1 && !$guide->published_at) {
            $data['published_at'] = now();
        }

        DB::transaction(function () use ($guide, $data, $req) {
            $guide->update($data,['status' => 0]);
            

            $tagIds = collect($req->input('tags', []))
                ->filter()
                ->map(fn ($v) => (int) $v)
                ->all();

            $guide->tags()->sync($tagIds);
        });

      return redirect()
    ->route('admin.guides.index')
    ->with('success', 'Đã cập nhật.');
    }

    public function destroy(Guide $guide)
    {
        $guide->delete();
        return back()->with('success', 'Đã xoá.');
    }

// Tệp: App\Http\Controllers\Admin\GuideController.php

public function togglePublish(Guide $guide)
{
    // Tính trạng thái mới
    $newStatus = $guide->status ? 0 : 1;

    $payload = ['status' => $newStatus];

    if ($newStatus === 1) {
        // Lần đầu xuất bản thì set published_at
        if (empty($guide->published_at)) {
            $payload['published_at'] = now();
        }
    } else {
        // Nếu muốn khi chuyển về nháp thì xóa thời điểm xuất bản:
        // $payload['published_at'] = null;

        // Hoặc nếu muốn giữ nguyên published_at thì bỏ dòng trên.
    }

    // update/save
    $guide->update($payload);

    return back()->with(
        'success',
        $newStatus === 1 ? 'Đã xuất bản bài viết.' : 'Đã chuyển bài viết về nháp.'
    );
}

}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuideTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuideTagController extends Controller
{
    public function index()
    {
        $tags = GuideTag::latest()->paginate(10);
        return view('dashboard.guides.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('dashboard.guides.tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:guide_tags,name',
        ]);

        GuideTag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('guide_tags.index')
            ->with('success', 'Thêm thẻ thành công!');
    }

    public function edit(GuideTag $guide_tag)
    {
        return view('dashboard.guides.tags.edit', ['tag' => $guide_tag]);
    }

    public function update(Request $request, GuideTag $guide_tag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:guide_tags,name,' . $guide_tag->id,
        ]);

        $guide_tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('guide_tags.index')
            ->with('success', 'Cập nhật thẻ thành công!');
    }

    public function destroy(GuideTag $guide_tag)
    {
        $guide_tag->delete();
        return redirect()->route('guide_tags.index')
            ->with('success', 'Xóa thẻ thành công!');
    }
}
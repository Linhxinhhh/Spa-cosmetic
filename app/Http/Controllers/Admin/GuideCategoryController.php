<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuideCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuideCategoryController extends Controller
{
    public function index()
    {
        $categories = GuideCategory::latest()->paginate(10);
        return view('dashboard.guides.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('dashboard.guides.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:guide_categories,name',
        ]);

        GuideCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('guide_categories.index')
            ->with('success', 'Thêm danh mục thành công!');
    }

    public function edit(GuideCategory $guide_category)
    {
        return view('dashboard.guides.categories.edit', ['category' => $guide_category]);
    }

    public function update(Request $request, GuideCategory $guide_category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:guide_categories,name,' . $guide_category->id,
        ]);

        $guide_category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('guide_categories.index')
            ->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(GuideCategory $guide_category)
    {
        $guide_category->delete();
        return redirect()->route('guide_categories.index')
            ->with('success', 'Xóa danh mục thành công!');
    }
}

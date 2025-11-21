<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all'); // 'all' | 'hoạt động' | 'ngưng hoạt động' | 1 | 0
        $level  = $request->query('level', '1');    // '1' | '2' | 'all'

        $normalizeToActive = function ($value) {
            if ($value === null || $value === '' || $value === 'all') return null;
            $v = mb_strtolower(trim((string)$value));
            return in_array($v, ['1','hoạt động','active','act','on','true','yes'], true) ? 1 : 0;
        };
        $active = $normalizeToActive($status);

        $query = ProductCategory::query()->whereNull('parent_id');

        if ($level === '2') {
            $query->whereHas('children');
        }

        if ($active !== null) {
            $query->where(function ($q) use ($active) {
                if ($active === 1) {
                    $q->where('status', 1)
                      ->orWhere('status', 'hoạt động')
                      ->orWhere('status', 'active');
                } else {
                    $q->where('status', 0)
                      ->orWhere('status', 'ngưng hoạt động')
                      ->orWhere('status', 'inactive');
                }
            });
        }

        $query->withCount('products')
              ->with(['children' => function ($child) use ($active) {
                  if ($active !== null) {
                      $child->where(function ($c) use ($active) {
                          if ($active === 1) {
                              $c->where('status', 1)
                                ->orWhere('status', 'hoạt động')
                                ->orWhere('status', 'active');
                          } else {
                              $c->where('status', 0)
                                ->orWhere('status', 'ngưng hoạt động')
                                ->orWhere('status', 'inactive');
                          }
                      });
                  }
                  $child->withCount('products');
              }]);

        $categories = $query->orderBy('created_at', 'desc')
                            ->paginate(10)
                            ->appends($request->only(['status', 'level']));

        $filters = ['status' => $status, 'level' => $level];
        return view('dashboard.categories.products.index', compact('categories', 'filters'));
    }

    public function create()
    {
        $parents = ProductCategory::orderBy('category_name')->get();
        return view('dashboard.categories.products.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:product_categories,category_name',
            'parent_id'     => 'nullable|exists:product_categories,category_id',
            'image'         => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
        ]);

        $data = [
            'category_name' => $request->category_name,
            'slug'          => Str::slug($request->category_name),
            'parent_id'     => $request->parent_id,
        ];

        // Upload ảnh nếu có
        if ($request->hasFile('image')) {
            // lưu vào storage/app/public/categories
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = $path;
        }

        ProductCategory::create($data);

        return redirect()
            ->route('admin.product_categories.index')
            ->with('success', 'Thêm danh mục thành công!');
    }

    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        $parents = ProductCategory::where('category_id', '!=', $id)
                    ->orderBy('category_name')->get();

        return view('dashboard.categories.products.edit', compact('category', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $category = ProductCategory::findOrFail($id);

        $request->validate([
            'category_name' => 'required|string|max:255|unique:product_categories,category_name,' . $id . ',category_id',
            'parent_id'     => 'nullable|exists:product_categories,category_id|not_in:' . $id,
            'image'         => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
        ]);

        $data = [
            'category_name' => $request->category_name,
            'slug'          => Str::slug($request->category_name),
             'parent_id'     => $request->filled('parent_id') ? $request->parent_id : $category->parent_id,
        ];

        // Nếu upload ảnh mới -> xóa ảnh cũ và lưu mới
        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()
            ->route('admin.product_categories.index')
            ->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $category = ProductCategory::with('children')->findOrFail($id);

        // Xoá ảnh của các danh mục con (nếu có)
        if ($category->children && $category->children->count() > 0) {
            foreach ($category->children as $child) {
                if ($child->image && Storage::disk('public')->exists($child->image)) {
                    Storage::disk('public')->delete($child->image);
                }
                $child->delete();
            }
        }

        // Xoá ảnh của danh mục hiện tại
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.product_categories.index')
            ->with('success', 'Xóa danh mục thành công!');
    }
}

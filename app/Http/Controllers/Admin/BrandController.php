<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('brand_id', 'desc')->paginate(10);
        return view('dashboard.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('dashboard.brands.create');
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'brand_name' => 'required|string|max:255',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'description' => 'nullable|string',
        'status' => 'required|in:0,1,2',
    ]);

    // Generate slug
    $slug = Str::slug($request->brand_name);
    $validated['slug'] = $slug;

    // Check unique slug
    if (Brand::where('slug', $slug)->exists()) {
        return back()->withInput()->with('error', 'Tên thương hiệu đã tồn tại.');
    }

    // Upload logo lên R2 nếu có
    if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $path = Storage::disk('r2')->put('brands', $file);
        $validated['logo'] = Storage::disk('r2')->url($path);
    }

    try {
        Brand::create($validated);
        return redirect()->route('admin.brands.index')->with('success', 'Thêm thương hiệu thành công');
    } catch (\Exception $e) {
        \Log::error('Error in store method: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Lỗi khi thêm thương hiệu');
    }
}


    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('dashboard.brands.edit', compact('brand'));
    }

   public function update(Request $request, $id)
{
    $brand = Brand::findOrFail($id);

    $validated = $request->validate([
        'brand_name' => 'required|string|max:255',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'description' => 'nullable|string',
        'status' => 'required|in:0,1,2',
    ]);

    // Generate slug
    $slug = Str::slug($request->brand_name);
    $validated['slug'] = $slug;

    if (Brand::where('slug', $slug)->where('brand_id', '!=', $brand->brand_id)->exists()) {
        return back()->withInput()->with('error', 'Tên thương hiệu đã tồn tại.');
    }

    // Upload logo mới lên R2
    if ($request->hasFile('logo')) {
        // Xóa logo cũ nếu cần (chỉ xóa nếu bạn lưu path đầy đủ trên R2)
        if ($brand->logo) {
            $oldPath = str_replace(Storage::disk('r2')->url(''), '', $brand->logo);
            Storage::disk('r2')->delete($oldPath);
        }

        $file = $request->file('logo');
        $path = Storage::disk('r2')->put('brands', $file);
        $validated['logo'] = Storage::disk('r2')->url($path);
    }

    try {
        $brand->update($validated);
        return redirect()->route('admin.brands.index')->with('success', 'Cập nhật thương hiệu thành công');
    } catch (\Exception $e) {
        \Log::error('Error in update method: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Lỗi khi cập nhật thương hiệu');
    }
}




    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            
            $brand->delete();
            return redirect()->route('admin.brands.index')->with('success', 'Xóa thương hiệu thành công');
        } catch (\Exception $e) {
            \Log::error('Error in destroy method: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi xóa thương hiệu: ' . $e->getMessage());
        }
    }
}
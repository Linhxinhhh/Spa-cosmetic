<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class ProductImageController extends Controller
{
    // Upload nhiều ảnh
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'images.*' => ['required', File::image()->types(['jpg','jpeg','png','webp'])->max(2*1024)]
        ]);

        $created = [];
        foreach ($request->file('images', []) as $i => $file) {
            // Lưu vào public/uploads/products/{product_id}
            $path = $file->store("uploads/products/{$product->product_id}", 'public');
            $url  = Storage::url($path);

            $created[] = ProductImage::create([
                'product_id' => $product->product_id,
                'url'        => $url,
                'sort_order' => ($product->imagesRel()->max('sort_order') ?? 0) + $i + 1,
                'is_main'    => $product->imagesRel()->count() === 0 && $i === 0, // nếu là ảnh đầu tiên thì set main
            ]);
        }

        return response()->json([
            'ok' => true,
            'items' => $created,
        ]);
    }

    // Xoá 1 ảnh
    public function destroy(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->product_id, 404);

        // cố gắng xoá file (nếu là URL public storage)
        $publicPrefix = asset('storage/');
        if (str_starts_with($image->url, $publicPrefix)) {
            $relative = ltrim(str_replace($publicPrefix, '', $image->url), '/');
            Storage::disk('public')->delete($relative);
        }

        $wasMain = $image->is_main;
        $image->delete();

        // nếu xoá ảnh đang main → set ảnh đầu tiên còn lại làm main
        if ($wasMain) {
            $first = $product->imagesRel()->first();
            if ($first) { $first->update(['is_main' => true]); }
        }

        return response()->json(['ok' => true]);
    }

    // Set main
    public function setMain(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->product_id, 404);

        ProductImage::where('product_id', $product->product_id)->update(['is_main' => false]);
        $image->update(['is_main' => true]);

        return response()->json(['ok' => true]);
    }

    // Lưu thứ tự sau drag-drop
    public function sort(Request $request, Product $product)
    {
        $data = $request->validate([
            'order' => 'required|array',                 // [image_id_1, image_id_2, ...]
            'order.*' => 'integer|exists:product_images,id'
        ]);

        foreach ($data['order'] as $index => $id) {
            ProductImage::where('id', $id)
                ->where('product_id', $product->product_id)
                ->update(['sort_order' => $index]);
        }
        return response()->json(['ok' => true]);
    }
}

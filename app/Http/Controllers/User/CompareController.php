<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompareController extends Controller
{
    
    /** Trang danh sách so sánh */
    public function index()
    {
        $compare  = session()->get('compare', []);
        $products = Product::whereIn('product_id', $compare)->get();

        return view('Users.compare.index', compact('products'));
    }
// Thêm
public function add(Product $product, Request $request)
{
    $compare = session()->get('compare', []);

    // ép về số & chặn giá trị rác/null
    $id = (int) ($product->product_id ?? 0);
    if ($id > 0 && !in_array($id, $compare, true)) {
        $compare[] = $id;
    }

    // làm sạch mảng: bỏ null/0/chuỗi rỗng và reindex
    $compare = array_values(array_filter($compare, fn($v) => !is_null($v) && (int)$v > 0));

    if (empty($compare)) {
        session()->forget('compare');
    } else {
        session()->put('compare', $compare);
    }

    $message = 'Đã thêm sản phẩm vào danh sách so sánh.';
    return $request->ajax()
        ? response()->json(['message' => $message, 'compare_count' => count($compare)])
        : back()->with('success', $message);
}

// Xóa
public function remove($id)
{
    $compare = session()->get('compare', []);

    // loại bỏ id + dọn rác luôn
    $compare = array_values(array_filter($compare, fn($pid) => (int)$pid > 0 && (int)$pid !== (int)$id));

    if (empty($compare)) {
        session()->forget('compare');
    } else {
        session()->put('compare', $compare);
    }

    return redirect()->route('users.compare.index')->with('success', 'Đã xóa khỏi danh sách so sánh.');
}

    /** Xóa 1 sản phẩm khỏi danh sách so sánh */
/** Xóa 1 sản phẩm khỏi danh sách so sánh */




}

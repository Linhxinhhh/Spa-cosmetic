<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // yêu cầu đăng nhập
    }
    public function index()
{
    $user = auth()->user();
    // lấy sản phẩm trong wishlist
    $products = $user->wishlist()->with('category')->paginate(12);

    return view('Users.wishlist.index', compact('products'));
}


public function toggle(Product $product)
{
    $user = auth()->user();

    // đang có?
    $exists = $user->wishlist()
                   ->wherePivot('product_id', $product->product_id)
                   ->exists();

    if ($exists) {
        $user->wishlist()->detach($product->product_id);
        $status  = 'removed';
        $message = 'Đã xóa khỏi danh sách yêu thích.';
    } else {
        $user->wishlist()->attach($product->product_id);
        $status  = 'added';
        $message = 'Đã thêm vào danh sách yêu thích.';
    }

    $count = $user->wishlist()->count();

    // AJAX: trả JSON
    if (request()->wantsJson()) {
        return response()->json([
            'status'  => $status,   // 'added' | 'removed'
            'message' => $message,
            'count'   => $count,    // tổng số sp trong wishlist -> để cập nhật badge, nếu muốn
        ]);
    }

    // non-AJAX fallback
    return back()->with('success', $message);
}

}

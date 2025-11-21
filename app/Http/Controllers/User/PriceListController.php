<?php
namespace App\Http\Controllers\User;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PriceListController extends Controller
{
  public function index(Request $request)
{
    $kw     = $request->input('q');
    $catId  = $request->input('category_id');
    $min    = $request->input('min_price');
    $max    = $request->input('max_price');
    $sort   = $request->input('sort', 'price_asc');

    $query = Service::query()
        ->active()
        ->when($catId, fn($q) => $q->where('category_id', $catId))
        ->search($kw)
        ->priceRange($min, $max);

    if ($sort === 'price_desc')    $query->orderBy('effective_price', 'desc');
    elseif ($sort === 'newest')    $query->orderBy('created_at', 'desc');
    else                           $query->orderBy('effective_price', 'asc');

    $services   = $query->paginate(12)->withQueryString();
    $categories = ServiceCategory::orderBy('category_name')->get(['category_id','category_name']);
      $minBooked = 2; 
    // === DỊCH VỤ BÁN CHẠY (dựa trên số lịch đã xác nhận/hoàn tất) ===
   $topServices = Service::active()
        ->withCount(['appointments as booked_count' => function ($q) {
            // tuỳ hệ thống của bạn: lọc các lịch đã xác nhận/hoàn tất
            $q->whereIn('status', [1, 2]); // bỏ whereIn nếu không có cột status
        }])
        ->having('booked_count', '>=', $minBooked)   // CHỈ lấy dịch vụ có >= 2/3 lượt đặt
        ->orderByDesc('booked_count')
        ->orderBy('service_name')
        ->take(6)   // số item trong sidebar
        ->get();

    return view('Users.services.pricelist', compact(
        'services','categories','catId','min','max','kw','sort','topServices','minBooked'
    ));
}
}

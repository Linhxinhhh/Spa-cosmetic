<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // add DB facade

// Sửa lại theo model thật sự của bạn
use App\Models\Order;
use App\Models\User;        // hoặc Customer
use App\Models\Appointment; // nếu có

class AnalyticsController extends Controller
{
    public function index()
    {
         $latestOrders = DB::table('orders')
           ->leftJoin('users', 'orders.user_id', '=', 'users.user_id')
            ->select('orders.*', 'users.name as customer_name')
            ->orderByDesc('order_id')
            ->limit(10)
            ->get();
        // Tổng doanh thu từ các đơn hàng đã thanh toán
        $totalRevenue = DB::table('orders')
            //->where('payment_status', 'paid')
            ->sum('total_amount');
        $totalProducts = DB::table('products')->count();
        // Tổng số đơn hàng
        $totalOrders = DB::table('orders')->count();
          $totalUsers = DB::table('users')->count();
        // Tổng số sản phẩm đã bán
        $totalProductsSold = DB::table('order_items')->sum('quantity');
         $totalReviews = DB::table('reviews')->count();

        // THÊM: Tổng số dịch vụ
        $totalServices = DB::table('services')->count();

        // Top 5 sản phẩm bán chạy
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.product_id')
            ->select('products.product_name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // SỬA: Top dịch vụ được đặt nhiều nhất (tính cả chưa xác nhận - giả sử dùng bảng appointments hoặc orders liên kết với services)
        // Giả sử có bảng 'appointments' với cột 'service_id' và 'status' (hoặc dùng orders nếu dịch vụ gắn với orders)
// Trong AnalyticsController::index()
$topServices = DB::table('appointments')
    ->join('services', 'appointments.service_id', '=', 'services.id') // Thay 'services.service_id' thành 'services.id' nếu PK là 'id'
    ->select(
        'services.service_name', 
        DB::raw('COUNT(DISTINCT appointments.user_id) as total_used') // Số unique users (lượt đặt)
    )
    // Bỏ whereIn tạm để test tất cả status (bao gồm pending)
    // ->whereIn('appointments.status', ['pending', 'confirmed', 'completed'])
    ->groupBy('services.id', 'services.service_name') // Group theo 'id' nếu thay trên
    ->having('total_used', '>', 0) // Chỉ lấy services có >0 lượt, tránh hiển thị 0
    ->orderByDesc('total_used') // Giảm dần từ nhiều nhất
    ->limit(15)
    ->get();
        // Nếu không có appointments, và orders_count là cột tĩnh trong services, thì cập nhật nó động:
        // DB::table('services')->update(['orders_count' => DB::raw('(SELECT COUNT(*) FROM appointments WHERE service_id = services.service_id)')]); // Nhưng tốt hơn dùng query động như trên

        $orderStatus = [
    'completed' => DB::table('orders')->where('status','completed')->count(),
    'shipping'  => DB::table('orders')->where('status','shipping')->count(),
    'pending'   => DB::table('orders')->where('status','pending')->count(),
    'cancel'    => DB::table('orders')->where('status','cancel')->count(),
];

        // SỬA: Doanh thu theo tháng (chỉ tháng hiện tại, theo ngày)
   $revenueMonthly = DB::table('orders')
    ->selectRaw("DAY(created_at) as day, SUM(total_amount) as revenue")
    ->whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year)
    ->groupBy('day')
    ->orderBy('day', 'ASC')
    ->get();

        $paymentMethodCount = DB::table('orders')
    ->select('payment_method', DB::raw('COUNT(*) as total'))
    ->groupBy('payment_method')
    ->pluck('total','payment_method');        

        return view('dashboard.analytics.index', compact(
    'totalRevenue',
    'totalProducts',
    'totalOrders',
    'totalProductsSold',
    'topProducts',
    'topServices',
    'revenueMonthly',
    'paymentMethodCount',
    'latestOrders',
    'orderStatus',
    'totalUsers',
    'totalReviews',
    'totalServices' // THÊM biến mới
));
    }
}
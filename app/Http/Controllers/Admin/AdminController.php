<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;



class AdminController extends Controller
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
        // Top 5 sản phẩm bán chạy
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.product_id')
            ->select('products.product_name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        $orderStatus = [
    'completed' => DB::table('orders')->where('status','completed')->count(),
    'shipping'  => DB::table('orders')->where('status','shipping')->count(),
    'pending'   => DB::table('orders')->where('status','pending')->count(),
    'cancel'    => DB::table('orders')->where('status','cancel')->count(),
];
        // Top dịch vụ được đặt nhiều nhất (dựa vào orders_count)
        $topServices = DB::table('services')
            ->select('service_name', 'orders_count')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get();

        // Doanh thu theo tháng (12 tháng gần nhất)
   $revenueMonthly = DB::table('orders')
    ->selectRaw("DATE(created_at) as day, SUM(total_amount) as revenue")
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
    'totalReviews'
));
    }
}

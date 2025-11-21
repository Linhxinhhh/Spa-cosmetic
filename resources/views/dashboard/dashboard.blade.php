@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Trang quản trị')
@section('page-title', 'Quản trị')

@section('content')
@include('dashboard.analytics.index', [
    'totalOrders' => $totalOrders,
    'totalRevenue' => $totalRevenue,
    'totalProductsSold' => $totalProductsSold,
    'totalUsers' => $totalUsers,
    'totalReviews' => $totalReviews,
    'ordersPerMonth' => $ordersPerMonth,
    'revenuePerMonth' => $revenuePerMonth,
    'topProducts' => $topProducts,
    'paymentMethodCount' => $paymentMethodCount,
    'latestOrders' => $latestOrders,
    'orderStatus' => $orderStatus,
])  


@endsection


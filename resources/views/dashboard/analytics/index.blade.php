@extends('dashboard.layouts.app')
@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Trang quản trị')
@section('page-title', 'Thống kê và phân tích')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --service-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); /* Màu mới cho dịch vụ */
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.15);
    }

    body {
        background: #f8f9fc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Stats Cards */
    .stats-row {
    display: flex;
    flex-wrap: nowrap;  
    gap: 16px;         
    overflow-x: auto;   
    padding-bottom: 4px;
}

.stats-col {
     width: 20%; /* Điều chỉnh từ 25% xuống 20% để fit 5 boxes */
    min-width: 200px; /* Giảm min-width để phù hợp hơn */
}
    .stats-box {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stats-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--card-gradient);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stats-box:hover::before {
        opacity: 1;
    }

    .stats-box.info { --card-gradient: var(--info-gradient); }
    .stats-box.danger { --card-gradient: var(--warning-gradient); }
    .stats-box.success { --card-gradient: var(--success-gradient); }
    .stats-box.warning { --card-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stats-box.service { --card-gradient: var(--service-gradient); } /* Lớp mới cho dịch vụ */

    .stats-icon {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 36px;
        margin-bottom: 20px;
        background: var(--card-gradient);
        color: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        position: relative;
    }

    .stats-icon::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(255,255,255,0.3), transparent);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stats-box:hover .stats-icon::after {
        opacity: 1;
    }

    .stats-content h5 {
        font-size: 36px;
        font-weight: 700;
        margin: 0 0 8px 0;
        color: #2d3748;
        background: #3b82f6;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stats-content small {
        color: #718096;
        font-size: 14px;
        font-weight: 500;
        display: block;
        margin-bottom: 12px;
    }

    .stats-link {
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: gap 0.3s;
    }

    .stats-link:hover {
        gap: 8px;
    }

    /* Chart Cards */
    .chart-card {
        background: #fff;
        padding: 28px;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
    }

    .chart-card:hover {
        box-shadow: var(--shadow-md);
    }

    .chart-card h5 {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 24px;
        position: relative;
        padding-bottom: 12px;
    }

    .chart-card h5::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: #3b82f6;
        border-radius: 2px;
    }

    #revenueChart {
        height: 350px !important;
    }

    #statusChart {
        height: 300px !important;
    }

    /* Table Card */
    .table-card {
        background: #fff;
        padding: 28px;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
        border: 1px solid rgba(0,0,0,0.04);
    }

    .table-card h5 {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-card h5::before {
        content: '';
        width: 15px;
        height: 24px;
        background: #3b82f6;
        border-radius: 2px;
    }

    .table {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-responsive {
    width: 100%;
}


    

    .table thead th {
        background:  #3b82f6 100%;
        color: #fff;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border: none;
          text-align: center !important;
        vertical-align: middle !important;
    }

    .table thead th:first-child {
        border-top-left-radius: 12px;
    }

    .table thead th:last-child {
        border-top-right-radius: 12px;
    }

    .table tbody tr {
        transition: all 0.2s;
    }

    .table tbody tr:hover {
        background: #f7fafc;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
        color: #4a5568;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status Badge */
    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-completed {
        background: #d4edda;
        color: #155724;
    }

    .status-shipping {
        background: #fff3cd;
        color: #856404;
    }

    .status-pending {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-cancel {
        background: #f8d7da;
        color: #721c24;
    }

    /* Top Products & Services */
    .product-item, .service-item { /* Thêm class chung cho service */
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        margin-bottom: 12px;
        background: #f7fafc;
        border-radius: 12px;
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }

    .product-item:hover, .service-item:hover {
        background: #edf2f7;
        border-left-color: #667eea;
        transform: translateX(4px);
    }

    .product-name, .service-name { /* Thêm class cho service */
        font-weight: 600;
        color: #2d3748;
        font-size: 14px;
    }

    .product-sales, .service-sales { /* Thêm class cho service */
        background: #3b82f6;
        color: #fff;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stats-box, .chart-card, .table-card {
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .stats-box:nth-child(1) { animation-delay: 0.1s; }
    .stats-box:nth-child(2) { animation-delay: 0.2s; }
    .stats-box:nth-child(3) { animation-delay: 0.3s; }
    .stats-box:nth-child(4) { animation-delay: 0.4s; }
    .stats-box:nth-child(5) { animation-delay: 0.5s; } /* Delay cho box mới */

    /* Responsive */
    @media (max-width: 768px) {
        .stats-box {
            margin-bottom: 16px;
        }
        
        .stats-icon {
            width: 56px;
            height: 56px;
            font-size: 24px;
        }
        
        .stats-content h5 {
            font-size: 24px;
        }

        .stats-col {
            width: 100%; /* Trên mobile, stack full width */
        }
    }
</style>

<div class="container-fluid mt-4">

    {{-- ==================== 5 BOX THỐNG KÊ (THÊM DỊCH VỤ) ==================== --}}
<div class="stats-row d-flex flex-nowrap">
    
    <div class="stats-col">
        <div class="stats-box info">
            <div class="stats-icon">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="stats-content">
                <h5>{{ $totalOrders }}</h5>
                <small>Tổng số đơn hàng</small>
                <a href="#" class="stats-link text-info">
                    Chi tiết <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="stats-col">
        <div class="stats-box danger">
            <div class="stats-icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stats-content">
                <h5>{{ $totalUsers }}</h5>
                <small>Thành viên</small>
                <a href="#" class="stats-link text-danger">
                    Chi tiết <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="stats-col">
        <div class="stats-box success">
            <div class="stats-icon">
                <i class="fa-solid fa-box"></i>
            </div>
            <div class="stats-content">
                <h5>{{ $totalProducts }}</h5>
                <small>Sản phẩm</small>
                <a href="#" class="stats-link text-success">
                    Chi tiết <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="stats-col">
        <div class="stats-box warning">
            <div class="stats-icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="stats-content">
                <h5>{{ $totalReviews }}</h5>
                <small>Đánh giá</small>
                <a href="#" class="stats-link text-warning">
                    Chi tiết <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- THÊM BOX THỐNG KÊ DỊCH VỤ --}}
    <div class="stats-col">
        <div class="stats-box service">
            <div class="stats-icon">
                <i class="fa-solid fa-cogs"></i> {{-- Icon phù hợp cho dịch vụ --}}
            </div>
            <div class="stats-content">
                <h5>{{ $totalServices ?? 0 }}</h5> {{-- Giả sử biến $totalServices từ controller --}}
                <small>Dịch vụ</small>
                <a href="#" class="stats-link text-danger">
                    Chi tiết <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

</div>




    {{-- ==================== BIỂU ĐỒ DOANH THU + TRẠNG THÁI ==================== --}}
    <div class="row">

        {{-- Biểu đồ doanh thu --}}
        <div class="col-md-8">
            <div class="chart-card">
                <h5 class="text-center">Biểu đồ doanh thu các ngày trong tháng</h5>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Biểu đồ tròn trạng thái --}}
        <div class="col-md-4">
            <div class="chart-card">
                <h5 class="text-center">Thống kê trạng thái đơn hàng</h5>
                <canvas id="statusChart"></canvas>
            </div>
        </div>

    </div>


    {{-- ==================== DANH SÁCH ĐƠN HÀNG MỚI ==================== --}}
    <div class="table-card">
        <h5>Danh sách đơn hàng mới</h5>

        <div class="table-responsive">
            <table class="table table-hover text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestOrders as $order)
                    <tr style="align-content: center">
                        <td style="align-content: center"><strong>#{{ $order->order_id }}</strong></td>
                        <td style="align-content: center">{{ $order->customer_name }}</td>
                        <td style="align-content: center"><strong>{{ number_format($order->total_amount) }}đ</strong></td>
                        <td >{{ $order->created_at }}</td>
                        <td>
                         <span class="status-badge 
                        @if($order->status == 'completed') status-completed
                            @elseif($order->status == 'shipping') status-shipping
                            @elseif($order->status == 'pending') status-pending
                            @else status-cancel
                            @endif">
                            
                            @switch($order->status)
                                @case('completed')
                                    Hoàn tất
                                    @break

                                @case('shipping')
                                    Đang vận chuyển
                                    @break

                                @case('pending')
                                    Đang xử lý
                                    @break

                                @case('cancel')
                                    Đã hủy
                                    @break

                                @default
                                    Không xác định
                            @endswitch
                        </span>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    {{-- ==================== TOP SẢN PHẨM BÁN CHẠY VÀ TOP DỊCH VỤ ==================== --}}
    <div class="row">
        <div class="col-md-6">
            <div class="table-card">
                <h5>Top sản phẩm bán chạy</h5>

                @foreach($topProducts as $p)
                <div class="product-item">
                    <div class="product-name">{{ $p->product_name }}</div>
                    <div class="product-sales">{{ $p->total_sold }} lượt mua</div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- THÊM PHẦN THỐNG KÊ TOP DỊCH VỤ --}}
        <div class="col-md-6">
            <div class="table-card">
                <h5>Top dịch vụ sử dụng nhiều</h5>

                @forelse($topServices ?? [] as $s) {{-- Giả sử biến $topServices từ controller --}}
                <div class="service-item">
                    <div class="service-name">{{ $s->service_name }}</div>
                    <div class="service-sales">{{ $s->total_used ?? 0 }} lượt sử dụng</div>
                </div>
                @empty
                <p class="text-muted text-center mt-3">Chưa có dữ liệu dịch vụ.</p>
                @endforelse

            </div>
        </div>
    </div>

</div>

{{-- ChartJS UMD --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
/* === Biểu đồ doanh thu === */
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: @json($revenueMonthly->pluck('day')),
        datasets: [{
            label: "Doanh thu",
            data: @json($revenueMonthly->pluck('revenue')),
            borderColor: "#667eea",
            backgroundColor: "rgba(102, 126, 234, 0.1)",
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: "#667eea",
            pointBorderColor: "#fff",
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                labels: {
                    font: {
                        size: 14,
                        weight: '600'
                    },
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14,
                    weight: '600'
                },
                bodyFont: {
                    size: 13
                },
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        size: 12
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

/* === Biểu đồ trạng thái đơn hàng === */
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ["Hoàn tất", "Đang vận chuyển", "Đang xử lý", "Hủy"],
        datasets: [{
            data: [
                {{ $orderStatus['completed'] ?? 0 }},
                {{ $orderStatus['shipping'] ?? 0 }},
                {{ $orderStatus['pending'] ?? 0 }},
                {{ $orderStatus['cancel'] ?? 0 }},
            ],
            backgroundColor: [
                "#38ef7d",
                "#fee140",
                "#00f2fe",
                "#f5576c"
            ],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        size: 13,
                        weight: '600'
                    },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8
            }
        },
        cutout: '65%'
    }
});
</script>

@endsection
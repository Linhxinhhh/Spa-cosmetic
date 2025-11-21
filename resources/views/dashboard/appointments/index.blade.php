
@extends('dashboard.layouts.app')

@section('page-title', 'Quản lý lịch hẹn')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        min-height: 100vh;
        color: #1e293b;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(10deg); }
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
    }

    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width:2px;
        height: 100%;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(59, 130, 246, 0.2);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Alerts */
    .alert-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-error {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 4px 15px rgba(29, 78, 216, 0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Table Container */
    .table-container {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.1);
        margin-bottom: 2rem;
    }

    .table-header {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

  /* === FIX TRÀN BẢNG CHO TRANG LỊCH HẸN === */

.table-responsive {
    overflow-x: auto;
    width: 100%;
}

/* Thu nhỏ bảng & chống tràn */
.modern-table {
    width: 100%;
    table-layout: fixed; /* CHIA ĐỀU CỘT */
    font-size: 0.85rem;  /* Thu nhỏ font */
}

/* Thu nhỏ padding để bảng gọn hơn */
.modern-table th,
.modern-table td {
    padding: 0.6rem 0.8rem;
    white-space: nowrap; 
    overflow: hidden;
    text-overflow: ellipsis; /* Hiện ... khi dài */
    text-align: center;
}

/* Chia width từng cột (tùy chỉnh để tránh tràn) */
.modern-table th:nth-child(1),
.modern-table td:nth-child(1) { width: 50px; }

.modern-table th:nth-child(2),
.modern-table td:nth-child(2) { width: 70px; align-content: center; }

.modern-table th:nth-child(3),
.modern-table td:nth-child(3) { width: 120px; }

.modern-table th:nth-child(4),
.modern-table td:nth-child(4) { width: 90px; }

.modern-table th:nth-child(5),
.modern-table td:nth-child(5) { width: 70px; }

.modern-table th:nth-child(6),
.modern-table td:nth-child(6) { width: 70px; }

.modern-table th:nth-child(7),
.modern-table td:nth-child(7) { width: 110px; }

.modern-table th:nth-child(8),
.modern-table td:nth-child(8) { width: 120px; }

.modern-table th:nth-child(9),
.modern-table td:nth-child(9) { width: 190px; }

/* Avatar và tên KH nằm gọn */
.customer-name {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.customer-avatar {
    min-width: 35px;
    min-height: 35px;
}


    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .status-pending {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
    }

    .status-confirmed {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .status-cancelled {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 8px;
        font_size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .delete-form {
        display: inline;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6b7280;
    }

    .empty-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .empty-description {
        font-size: 1rem;
        color: #6b7280;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    .pagination {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .pagination a,
    .pagination span {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .pagination a {
        background: white;
        color: #3b82f6;
        border: 1px solid #e2e8f0;
    }

    .pagination a:hover {
        background: #3b82f6;
        color: white;
        transform: translateY(-1px);
    }

    .pagination .current {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: 1px solid #3b82f6;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }

        .page-title {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .modern-table {
            font-size: 0.8rem;
        }

        .modern-table th,
        .modern-table td {
            padding: 0.75rem 1rem;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.25rem;
        }

        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }
    }

    /* Smooth animations */
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .pagination-box {
    background: #ffffff;
    border-top: 1px solid #e5e7eb;
    border-radius: 0 0 12px 12px;
}

/* Container bọc phân trang */
.pagination-wrapper nav {
    display: inline-flex;
    background: #f8fafc;
    padding: 6px 14px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

/* Nút phân trang */
.pagination-wrapper .page-link {
    border: none !important;
    color: #334155;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 8px;
}

/* Nút active màu xanh giống hình */
.pagination-wrapper .page-item.active .page-link {
    background: #2563eb !important;
    color: #fff !important;
    border-radius: 8px;
}

/* Vô hiệu hóa */
.pagination-wrapper .page-item.disabled .page-link {
    color: #cbd5e1 !important;
}

/* Hover */
.pagination-wrapper .page-link:hover {
    background: #e2e8f0;
    color: #1e293b;
}

</style>

<div class="container fade-in">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-calendar-alt"></i>
            Quản lý lịch hẹn
        </h1>
        <p class="page-subtitle">Theo dõi và quản lý lịch hẹn của Lyn & Spa</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-number">{{ $appointments->count() }}</div>
            <div class="stat-label">Tổng lịch hẹn</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div class="stat-number">{{ $appointments->where('appointment_date', '>=', now()->startOfMonth())->count() }}</div>
            <div class="stat-label">Lịch hẹn mới tháng này</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $appointments->where('status', 'confirmed')->count() }}</div>
            <div class="stat-label">Lịch hẹn đã xác nhận</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-number">{{ $appointments->where('status', 'cancelled')->count() }}</div>
            <div class="stat-label">Lịch hẹn đã hủy</div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert-success" id="successAlert">
            <i class="fas fa-check-circle"></i>
            <span id="successMessage">{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="alert-error" id="errorAlert">
            <i class="fas fa-exclamation-circle"></i>
            <span id="errorMessage">{{ $errors->first() }}</span>
        </div>
    @endif

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-list"></i>
                Danh sách lịch hẹn
            </h3>
        </div>
        
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Khách hàng</th>
                        <th>Dịch vụ</th>
                        <th>Ngày</th>
                        <th>Bắt đầu</th>
                        <th>Kết thúc</th>
                        <th>Trạng thái</th>
                        
                        <th style="text-align:">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->appointment_id }}</td>
                        <td>
                            <div class="customer-name">
                               
                                <div class="text-center" style="">{{ $appointment->user->name ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>{{ $appointment->service->service_name ?? 'N/A' }}</td>
                        <td><div>{{ $appointment->appointment_date?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')  }}</div>
                          </td>
                        <td>{{ $appointment->start_time }}</td>
                        <td>{{ $appointment->end_time }}</td>
                        <td>
                            <span class="status-badge 
                                {{ $appointment->status == 'confirmed' ? 'status-confirmed' : 
                                   ($appointment->status == 'pending' ? 'status-pending' : 'status-cancelled') }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                      
                        <td>
                            <div class="action-buttons">
                         
                             
                                <a href="{{ route('admin.appointments.edit', $appointment->appointment_id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                    Sửa
                                </a>
                                <form class="delete-form" action="{{ route('admin.appointments.destroy', $appointment->appointment_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h3 class="empty-title">Chưa có lịch hẹn nào</h3>
                                <p class="empty-description">Hệ thống chưa có dữ liệu lịch hẹn để hiển thị</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                
            </table>
            


@if($appointments instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="bg-white border-t border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">

        <!-- TEXT HIỂN THỊ -->
        <div class="text-sm text-gray-700">
            Hiển thị 
            <span class="font-medium">{{ $appointments->firstItem() }}</span> 
            đến 
            <span class="font-medium">{{ $appointments->lastItem() }}</span> 
            trong tổng số 
            <span class="font-medium">{{ $appointments->total() }}</span> 
            kết quả
        </div>

        <!-- PAGINATION BUTTONS -->
        <div class="flex items-center space-x-2">

            {{-- Nút Trước --}}
            <a href="{{ $appointments->previousPageUrl() ?? '#' }}"
               class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 
               {{ $appointments->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                <i class="fas fa-chevron-left mr-1"></i> Trước
            </a>

            {{-- Trang hiện tại --}}
            <span class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                {{ $appointments->currentPage() }}
            </span>

            {{-- Nút Sau --}}
            <a href="{{ $appointments->nextPageUrl() ?? '#' }}"
               class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 
               {{ !$appointments->hasMorePages() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                Sau <i class="fas fa-chevron-right ml-1"></i>
            </a>

        </div>

    </div>
</div>
@endif



        </div>
    </div>

 

</div>

@section('scripts')
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add staggered animation to table rows
        const rows = document.querySelectorAll('.modern-table tbody tr');
        rows.forEach((row, index) => {
            row.style.animationDelay = `${index * 0.1}s`;
            row.classList.add('fade-in');
        });

        // Add hover effects to stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Auto-hide alerts
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 4000);
        }
        
        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.display = 'none';
            }, 4000);
        }

        // Enhanced delete confirmation
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('Bạn có chắc chắn muốn xóa lịch hẹn này?\nHành động này không thể hoàn tác.')) {
                    const button = this.querySelector('button');
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xóa...';
                    button.disabled = true;
                    
                    // Submit form after showing loading state
                    setTimeout(() => {
                        this.submit();
                    }, 1500);
                }
            });
        });
    });
</script>
@endsection
@endsection

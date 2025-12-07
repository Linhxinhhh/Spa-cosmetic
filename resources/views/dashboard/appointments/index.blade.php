
@extends('dashboard.layouts.app')

@section('page-title', 'Quản lý lịch hẹn')

@section('content')

@push('styles')

<link href="{{asset('admin/giaodien/css/style.css')}}" rel="stylesheet">
@endpush
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

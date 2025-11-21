
@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị danh mục dịch vụ')
@section('page-title', 'Danh mục dịch vụ')


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

    /* Header Section */
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
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        width: 4px;
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
        background: linear-gradient(135deg, #FF0000, #B91C1C);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
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

    .table-responsive {
        overflow-x: auto;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .modern-table th {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 1rem 1.5rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .modern-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        transition: all 0.2s ease;
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background: rgba(59, 130, 246, 0.04);
        transform: scale(1.01);
    }

    /* Customer Info Styling */
    .customer-name {
        font-weight: 600;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .customer-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .customer-email {
        color: #6b7280;
        font-size: 0.85rem;
    }

    .customer-phone {
        color: #374151;
        font-weight: 500;
    }

    .loyalty-points {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
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
        font-size: 0.85rem;
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
</style>

<div class="container fade-in">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-users"></i>
            Quản lý khách hàng
        </h1>
        <p class="page-subtitle">Theo dõi và quản lý thông tin khách hàng của Lyn & Spa</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ $customers->count() }}</div>
            <div class="stat-label">Tổng khách hàng</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-number">{{ $customers->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
            <div class="stat-label">Khách hàng mới tháng này</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-number">{{ $customers->sum('loyalty_points') }}</div>
            <div class="stat-label">Tổng điểm tích lũy</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-birthday-cake"></i>
            </div>
            <div class="stat-number">{{ $customers->where('birthday', '>=', now()->startOfMonth())->where('birthday', '<=', now()->endOfMonth())->count() }}</div>
            <div class="stat-label">Sinh nhật tháng này</div>
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
                Danh sách khách hàng
            </h3>
        </div>
        
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Khách hàng</th>
                        <th>Liên hệ</th>
                        <th>Địa chỉ</th>
                        <th>Sinh nhật</th>
                        <th>Điểm tích lũy</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
@forelse($customers as $c)
  <tr>
    <td>{{ $c->id }}</td>

    {{-- Tên khách hàng --}}
   <td class="fw-semibold">
  {{ $c->name ?? optional($c->user)->name ?? '—' }}
</td>

    {{-- Liên hệ --}}
 <td>
  <div class="small text-muted">
    <i class="bi bi-envelope"></i>
    {{ $c->email ?? optional($c->user)->email ?? '—' }}
  </div>
  <div class="small text-muted">
    <i class="bi bi-telephone"></i>
    {{ $c->phone ?? optional($c->user)->phone ?? '—' }}
  </div>
</td>
{{-- Địa chỉ / Sinh nhật / Điểm --}}
<td>{{ $c->address ?? optional($c->user)->address ?? '—' }}</td>
@php
 


    $raw = $c->birthday;
    $birthdayText = '—';

    if (!empty($raw)) {
        try {
            if ($raw instanceof \Carbon\CarbonInterface) {
                $birthdayText = $raw->format('d/m/Y');
            } elseif (str_contains((string)$raw, '/')) {
                // DB lưu kiểu d/m/Y hoặc d/m/Y H:i:s
                $fmt = str_contains((string)$raw, ' ') ? 'd/m/Y H:i:s' : 'd/m/Y';
                $birthdayText = \Carbon\Carbon::createFromFormat($fmt, $raw)->format('d/m/Y');
            } else {
                // DB lưu Y-m-d hoặc Y-m-d H:i:s
                $birthdayText = \Carbon\Carbon::parse($raw)->format('d/m/Y');
            }
        } catch (\Throwable $e) {
            // Parse lỗi thì hiển thị nguyên văn
            $birthdayText = (string)$raw;
        }
    }
@endphp

<td>{{ $birthdayText }}</td>


<td>{{ number_format((int) $c->loyalty_points) }}</td>

    {{-- Hành động --}}
    <td>
      <div class="action-buttons">
        <a href="{{ route('admin.customers.edit', $c) }}" class="btn btn-primary">
          <i class="fas fa-edit"></i> Sửa
        </a>
<form class="delete-form"
      action="{{ route('admin.customers.destroy', $c) }}"
      method="POST"
      data-name="{{ $c->name ?? '' }}"
      onsubmit="return confirm('Bạn có chắc muốn xóa {{ $c->name ?? 'khách hàng này' }}? Hành động này không thể hoàn tác.');">
  @csrf
  @method('DELETE')
  <button type="submit" class="btn btn-danger">
    <i class="fas fa-trash"></i> Xóa
  </button>
</form>
      </div>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="7" class="text-center text-muted py-4">Chưa có khách hàng</td>
  </tr>
@endforelse
</tbody>


           
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        <div class="pagination">
            {{ $customers->links( ) }}
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
                
                if (confirm('Bạn có chắc chắn muốn xóa khách hàng này?\nHành động này không thể hoàn tác.')) {
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
      document.addEventListener('submit', function (e) {
    if (!e.target.matches('.delete-form')) return;

    e.preventDefault();
    const form = e.target;
    const name = form.dataset.name ? ` "${form.dataset.name}"` : '';
    const ok = confirm(`Bạn có chắc muốn xóa${name}? Hành động này không thể hoàn tác.`);
    if (ok) form.submit();
  });
</script>
@endsection
@endsection

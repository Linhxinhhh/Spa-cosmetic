@extends('dashboard.layouts.app')
@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị phân quyền')
@section('page-title', 'Danh sách phân quyền')
@section('content')
<style>
    :root {
        --primary-color: #6366f1;
        --primary-dark: #4f46e5;
        --secondary-color: #f8fafc;
        --accent-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --border-color: #e5e7eb;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .user-management-container {
        padding: 24px;
        max-width: 1400px;
        margin: 0 auto;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }

    .header-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title::before {
        content: "👥";
        font-size: 24px;
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin: 8px 0 0 0;
        font-size: 16px;
    }

    .stats-row {
        display: flex;
        gap: 16px;
        margin: 20px 0;
        flex-wrap: wrap;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 16px 20px;
        border-radius: 12px;
        flex: 1;
        min-width: 150px;
        text-align: center;
        box-shadow: var(--shadow);
    }

    .stat-number {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .table-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .table-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        padding: 20px 24px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .search-box {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 8px 12px;
        gap: 8px;
        backdrop-filter: blur(10px);
    }

    .search-box input {
        background: transparent;
        border: none;
        color: white;
        outline: none;
        width: 200px;
        font-size: 14px;
    }

    .search-box input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .table thead {
        background: #f8fafc;
    }

    .table th {
        padding: 16px 20px;
        text-align: left;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-color);
    }

    .table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .table tbody tr:hover {
        background: linear-gradient(90deg, #f0f9ff 0%, #e0f2fe 100%);
        transform: translateX(2px);
    }

    .table tbody tr:last-child {
        border-bottom: none;
    }

    .table td {
        padding: 16px 20px;
        vertical-align: middle;
        font-size: 14px;
    }

    .user-id {
        font-weight: 600;
        color: var(--primary-color);
        background: #f0f9ff;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
        font-size: 12px;
    }

    .user-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 2px;
    }

    .user-email {
        color: var(--text-secondary);
        font-size: 13px;
    }

    .role-badges {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .role-badge {
        background: linear-gradient(135deg, var(--accent-color) 0%, #059669 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .role-badge.admin {
        background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
    }

    .role-badge.editor {
        background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        color: white;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: var(--text-primary);
    }

    .pagination-wrapper {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border-radius: 8px;
        text-decoration: none;
        background: #f8fafc;
        color: var(--text-secondary);
        transition: all 0.2s ease;
        font-size: 14px;
        min-width: 40px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pagination a:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-1px);
    }

    .pagination .active span {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 1024px) {
        .user-management-container {
            padding: 16px;
        }
        
        .stats-row {
            flex-direction: column;
        }
        
        .table-header {
            flex-direction: column;
            gap: 16px;
            align-items: stretch;
        }
        
        .search-box {
            width: 100%;
        }
        
        .search-box input {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .table-wrapper {
            overflow-x: auto;
        }
        
        .table th,
        .table td {
            padding: 12px;
            font-size: 13px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .role-badges {
            justify-content: flex-start;
        }
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-secondary);
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="user-management-container">
    <!-- Header Section -->
    <div class="header-section">
        <h1 class="page-title">Quản lý quyền người dùng</h1>
        <p class="page-subtitle">Quản lý vai trò và quyền hạn của người dùng trong hệ thống</p>
        
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-number">{{ $users->total() }}</div>
                <div class="stat-label">Số lượng quản trị </div>
            </div>
          
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);">
                <div class="stat-number">{{ $users->groupBy('roles')->count() }}</div>
                <div class="stat-label">Vai trò</div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-wrapper">
        <div class="table-header">
            <h3 class="table-title">Danh sách người dùng</h3>
            <div class="search-box">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
                <input type="text" placeholder="Tìm kiếm người dùng..." id="searchInput">
            </div>
        </div>
        
        @if($users->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Thông tin người dùng</th>
                    <th>Vai trò</th>
               
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr data-user-id="{{ $user->user_id }}">
                    <td>
                        <span class="user-id">#{{ $user->user_id }}</span>
                    </td>
                    <td>
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                    </td>
                    <td>
                        <div class="role-badges">
                            @if($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                    <span class="role-badge {{ strtolower($role->name) }}">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="role-badge" style="background: #94a3b8;">Chưa có vai trò</span>
                            @endif
                        </div>
                    </td>
        
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.user_roles.edit', $user) }}" class="btn btn-primary">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                </svg>
                                Sửa quyền
                            </a>
                            
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">👤</div>
            <h3>Không có người dùng nào</h3>
            <p>Hiện tại chưa có người dùng nào trong hệ thống.</p>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination">
            {{ $users->links() }}
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('tbody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableRows.forEach(row => {
                const userName = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                const userEmail = row.querySelector('.user-email')?.textContent.toLowerCase() || '';
                const userId = row.dataset.userId || '';
                
                if (userName.includes(searchTerm) || 
                    userEmail.includes(searchTerm) || 
                    userId.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Add loading state to buttons
    document.querySelectorAll('.btn-primary').forEach(btn => {
        btn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="loading"></span> Đang xử lý...';
            this.style.pointerEvents = 'none';
            
            // Reset after 3 seconds (in case navigation doesn't happen)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            }, 3000);
        });
    });
});
</script>
@endsection
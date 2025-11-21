@extends('dashboard.layouts.app')
@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị phân quyền')
@section('page-title', 'Sửa phân quyền')
@section('content')
<style>
    :root {
        --primary-color: #6366f1;
        --primary-dark: #4f46e5;
        --secondary-color: #f8fafc;
        --success-color: #10b981;
        --success-dark: #059669;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --info-color: #3b82f6;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --border-color: #e5e7eb;
        --bg-light: #f9fafb;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .edit-roles-container {
        padding: 24px;
        max-width: 1200px;
        margin: 0 auto;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        text-decoration: none;
        margin-bottom: 20px;
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.2s ease;
        background: white;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow);
    }

    .back-button:hover {
        color: var(--primary-color);
        background: #f0f9ff;
        border-color: var(--primary-color);
        transform: translateX(-2px);
    }

    .page-header {
        background: white;
        border-radius: 20px;
        padding: 32px;
        margin-bottom: 28px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color) 0%, var(--info-color) 50%, var(--success-color) 100%);
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title::before {
        content: "👤";
        font-size: 24px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--info-color) 100%);
        padding: 8px;
        border-radius: 8px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-info {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 20px;
        background: var(--bg-light);
        padding: 20px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        margin-top: 16px;
    }

    .user-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--info-color) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 20px;
        box-shadow: var(--shadow);
    }

    .user-details h3 {
        margin: 0 0 4px 0;
        color: var(--text-primary);
        font-size: 18px;
        font-weight: 600;
    }

    .user-details p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 14px;
    }

    .form-container {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border-color);
    }

    .form-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 24px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-title::before {
        content: "🔐";
        font-size: 20px;
    }

    .roles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .role-card {
        background: var(--bg-light);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        min-height: 180px;
    }

    .role-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #94a3b8 0%, #64748b 100%);
        transition: all 0.3s ease;
    }

    .role-card:hover {
        border-color: var(--primary-color);
        background: #f0f9ff;
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .role-card.checked {
        border-color: var(--success-color);
        background: #f0fdf4;
    }

    .role-card.checked::before {
        background: linear-gradient(90deg, var(--success-color) 0%, var(--success-dark) 100%);
    }

    .role-card.admin::before {
        background: linear-gradient(90deg, var(--danger-color) 0%, #dc2626 100%);
    }

    .role-card.editor::before {
        background: linear-gradient(90deg, var(--warning-color) 0%, #d97706 100%);
    }

    .role-card.user::before {
        background: linear-gradient(90deg, var(--info-color) 0%, #1d4ed8 100%);
    }

    .role-checkbox {
        position: absolute;
        opacity: 0;
    }

    .role-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .role-name {
        font-size: 20px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .role-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
        box-shadow: var(--shadow);
    }

    .role-card.admin .role-icon {
        background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
    }

    .role-card.editor .role-icon {
        background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
    }

    .role-card.user .role-icon {
        background: linear-gradient(135deg, var(--info-color) 0%, #1d4ed8 100%);
    }

    .custom-checkbox {
        width: 24px;
        height: 24px;
        border: 2px solid var(--border-color);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background: white;
    }

    .role-card.checked .custom-checkbox {
        background: var(--success-color);
        border-color: var(--success-color);
    }

    .custom-checkbox svg {
        width: 16px;
        height: 16px;
        color: white;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .role-card.checked .custom-checkbox svg {
        opacity: 1;
    }

    .role-description {
        color: var(--text-secondary);
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .role-permissions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .permission-tag {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary-color);
        padding: 6px 10px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 500;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 24px;
        border-top: 1px solid var(--border-color);
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        min-width: 120px;
        justify-content: center;
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success-color) 0%, var(--success-dark) 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .btn-secondary {
        background: white;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-light);
        color: var(--text-primary);
        border-color: var(--text-secondary);
    }

    .loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .alert-warning {
        background: #fefce8;
        border: 1px solid #fef3c7;
        color: #a16207;
    }

    @media (max-width: 1024px) {
        .roles-grid {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .edit-roles-container {
            padding: 16px;
        }
        
        .page-header,
        .form-container {
            padding: 24px;
        }
        
        .roles-grid {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column-reverse;
        }
        
        .btn {
            width: 100%;
        }
        
        .user-info {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 16px;
        }
    }
</style>

<div class="edit-roles-container">
    <!-- Back Button -->
    <a href="{{ route('admin.user_roles.index') }}" class="back-button">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-8-8a1 1 0 010-1.414l8-8a1 1 0 011.414 1.414L2.414 9H17a1 1 0 110 2H2.414l7.293 7.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
        </svg>
        Quay lại danh sách
    </a>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Chỉnh sửa phân quyền</h1>
        
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="user-details">
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->email }}</p>
                @if($user->roles->count() > 0)
                    <p><strong>Vai trò hiện tại:</strong> {{ $user->roles->pluck('name')->join(', ') }}</p>
                @else
                    <p><em>Chưa có vai trò nào được gán</em></p>
                @endif
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <h2 class="form-title">Chọn vai trò cho người dùng</h2>
        
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if($roles->count() == 0)
            <div class="alert alert-warning">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Không có vai trò nào được định nghĩa trong hệ thống.
            </div>
        @else
            <form action="{{ route('admin.user_roles.update', $user) }}" method="POST" id="roleForm">
                @csrf
                @method('PUT')

                <div class="roles-grid">
                    @foreach($roles as $role)
                        @php
                            $roleClass = strtolower($role->name);
                            $isChecked = $user->roles->contains($role);
                            
                            // Define role descriptions and permissions
                            $roleData = [
                                'admin' => [
                                    'icon' => '👑',
                                    'description' => 'Quyền quản trị toàn bộ hệ thống, có thể thực hiện mọi thao tác.',
                                    'permissions' => ['Quản lý người dùng', 'Quản lý vai trò', 'Cấu hình hệ thống', 'Xem báo cáo']
                                ],
                                'editor' => [
                                    'icon' => '✏️',
                                    'description' => 'Có thể chỉnh sửa nội dung và quản lý một số chức năng cơ bản.',
                                    'permissions' => ['Chỉnh sửa nội dung', 'Quản lý media', 'Xem thống kê']
                                ],
                                'user' => [
                                    'icon' => '👤',
                                    'description' => 'Người dùng thông thường với quyền truy cập cơ bản.',
                                    'permissions' => ['Xem nội dung', 'Cập nhật hồ sơ']
                                ]
                            ];
                            
                            $currentRole = $roleData[$roleClass] ?? [
                                'icon' => '🔑',
                                'description' => 'Vai trò tùy chỉnh trong hệ thống.',
                                'permissions' => ['Quyền tùy chỉnh']
                            ];
                        @endphp
                        
                        <div class="role-card {{ $roleClass }} {{ $isChecked ? 'checked' : '' }}" onclick="toggleRole({{ $role->role_id }})">
                            <input type="checkbox" 
                                   name="roles[]" 
                                   value="{{ $role->role_id }}" 
                                   id="role_{{ $role->role_id }}" 
                                   class="role-checkbox"
                                   {{ $isChecked ? 'checked' : '' }}>
                            
                            <div class="role-header">
                                <div class="role-name">
                                    <div class="role-icon">{{ $currentRole['icon'] }}</div>
                                    {{ ucfirst($role->name) }}
                                </div>
                                <div class="custom-checkbox">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="role-description">
                                {{ $currentRole['description'] }}
                            </div>
                            
                            <div class="role-permissions">
                                @foreach($currentRole['permissions'] as $permission)
                                    <span class="permission-tag">{{ $permission }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.user_roles.index') }}" class="btn btn-secondary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Cập nhật quyền
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle role selection
    window.toggleRole = function(roleId) {
        const checkbox = document.getElementById(`role_${roleId}`);
        const card = checkbox.closest('.role-card');
        
        checkbox.checked = !checkbox.checked;
        
        if (checkbox.checked) {
            card.classList.add('checked');
        } else {
            card.classList.remove('checked');
        }
    };
    
    // Prevent card click when clicking on checkbox
    document.querySelectorAll('.role-checkbox').forEach(checkbox => {
        checkbox.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Form submission with loading state
    const form = document.getElementById('roleForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading"></span> Đang cập nhật...';
            submitBtn.disabled = true;
            
            // Reset after 5 seconds if form doesn't submit
            setTimeout(() => {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            }, 5000);
        });
    }
    
    // Add smooth animations to role cards
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    document.querySelectorAll('.role-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});
</script>
@endsection
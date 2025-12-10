@extends('dashboard.layouts.app')

@section('breadcrumb-parent', 'Quản trị')
@section('breadcrumb-child', 'Quản trị danh mục dịch vụ')
@section('page-title', 'Danh mục dịch vụ')


@section('content')
  @push('styles')

<link href="{{asset('admin/giaodien/css/style.css')}}" rel="stylesheet">
@endpush

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
                <div class="stat-number">
                    {{ $customers->where('birthday', '>=', now()->startOfMonth())->where('birthday', '<=', now()->endOfMonth())->count() }}
                </div>
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
                                <td>{{ $c->user_id }}</td>

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
                                            } elseif (str_contains((string) $raw, '/')) {
                                                // DB lưu kiểu d/m/Y hoặc d/m/Y H:i:s
                                                $fmt = str_contains((string) $raw, ' ') ? 'd/m/Y H:i:s' : 'd/m/Y';
                                                $birthdayText = \Carbon\Carbon::createFromFormat($fmt, $raw)->format('d/m/Y');
                                            } else {
                                                // DB lưu Y-m-d hoặc Y-m-d H:i:s
                                                $birthdayText = \Carbon\Carbon::parse($raw)->format('d/m/Y');
                                            }
                                        } catch (\Throwable $e) {
                                            // Parse lỗi thì hiển thị nguyên văn
                                            $birthdayText = (string) $raw;
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
                                        <form class="delete-form" action="{{ route('admin.customers.destroy', $c) }}"
                                            method="POST" data-name="{{ $c->name ?? '' }}"
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
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    @section('scripts')
        <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Add staggered animation to table rows
                const rows = document.querySelectorAll('.modern-table tbody tr');
                rows.forEach((row, index) => {
                    row.style.animationDelay = `${index * 0.1}s`;
                    row.classList.add('fade-in');
                });

                // Add hover effects to stat cards
                const statCards = document.querySelectorAll('.stat-card');
                statCards.forEach(card => {
                    card.addEventListener('mouseenter', function () {
                        this.style.transform = 'translateY(-5px) scale(1.02)';
                    });

                    card.addEventListener('mouseleave', function () {
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
                    form.addEventListener('submit', function (e) {
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
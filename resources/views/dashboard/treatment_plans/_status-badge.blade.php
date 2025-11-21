@php
    // Map màu
    $mapColor = [
        'draft'     => 'secondary',
        'active'    => 'primary',
        'scheduled' => 'info',
        'confirmed' => 'success',
        'completed' => 'success',
        'canceled'  => 'danger',
        'missed'    => 'warning',
        'expired'   => 'dark',
    ];

    // Map tiếng Việt
    $mapText = [
        'draft'     => 'Nháp',
        'active'    => 'Hoạt động',
        'scheduled' => 'Đã lên lịch',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn thành',
        'canceled'  => 'Đã hủy',
        'missed'    => 'Bỏ lỡ',
        'expired'   => 'Hết hạn',
    ];

    $color = $mapColor[$status] ?? 'secondary';
    $label = $mapText[$status] ?? ucfirst($status);
@endphp

<span class="badge bg-{{ $color }}">{{ $label }}</span>

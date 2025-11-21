<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\CustomerSyncService;

class OrderObserver
{
    // Khi tạo đơn -> chờ thanh toán
    public function created(Order $order): void
    {
        CustomerSyncService::touchFromOrder($order, false);
    }

    // Khi cập nhật trạng thái thanh toán -> trả tiền thành công
    public function updated(Order $order): void
    {
        // Chỉ khi payment_status thực sự đổi sang 'paid'
        if ($order->wasChanged('payment_status') && $order->payment_status === 'paid') {
            CustomerSyncService::touchFromOrder($order, true);
        }

        // Nếu dự án bạn xài cột 'status' thay vì 'payment_status'
        if ($order->wasChanged('status') && $order->status === 'paid') {
            CustomerSyncService::touchFromOrder($order, true);
        }
    }
}

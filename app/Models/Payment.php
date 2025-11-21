<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'provider',
        'order_code',
        'order_id',
        'amount',
        'currency',
        'txn_ref',
        'status',
        'request_payload',
        'callback_payload',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'callback_payload' => 'array',
    ];

    /**
     * Liên kết đến Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Kiểm tra xem payment đã thành công chưa
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Kiểm tra xem payment đang chờ xử lý
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Kiểm tra payment thất bại hoặc bị hủy
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'cancel']);
    }
}

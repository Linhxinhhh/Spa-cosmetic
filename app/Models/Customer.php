<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // ❌ Đang sai: 'birthday' => 'date' không thuộc fillable
    // ✅ Đúng: chỉ liệt kê tên cột
    protected $fillable = [
        'user_id', 'email', 'phone', 'address',
        'orders_count', 'total_spent',
        'last_order_id', 'last_order_at', 'last_status',
        'birthday', 'loyalty_points',
    ];

    // ✅ Cast birthday để Eloquent trả về Carbon
    protected $casts = [
        'orders_count'   => 'integer',
        'total_spent'    => 'decimal:2',
        'last_order_at'  => 'datetime',
        'loyalty_points' => 'integer',
        'birthday'       => 'date',      // DB nên lưu 'Y-m-d'
    ];

public function user()
{
    // FK ở customers: user_id  ->  PK ở users: user_id
    return $this->belongsTo(\App\Models\User::class, 'user_id', 'user_id');
}
 public function treatmentPlans()
    {
        return $this->hasMany(TreatmentPlan::class, 'customer_id', 'id');
    }
}

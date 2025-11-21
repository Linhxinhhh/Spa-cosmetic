<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id','order_code','total_amount',
        'payment_method','payment_status','status',
        'shipping_address','phone','note',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    // Map hiển thị
    public const STATUS = [
        'pending'    => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'shipped'    => 'Đang giao',
        'delivered'  => 'Hoàn thành',
        'cancelled'  => 'Đã hủy',
        'refunded'   => 'Hoàn tiền',
    ];
    public const PAYMENT_STATUS = [
        'pending' => 'Chưa thanh toán',
        'paid'    => 'Đã thanh toán',
        'failed'  => 'Thanh toán lỗi',
    ];
    public const PAYMENT_METHOD = [
        'cod'   => 'Tiền mặt (COD)',
        'momo'  => 'MoMo',
        'vnpay' => 'VNPAY',
    ];

    // Accessors
    public function getStatusLabelAttribute(){ return self::STATUS[$this->status] ?? $this->status; }
    public function getPaymentStatusLabelAttribute(){ return self::PAYMENT_STATUS[$this->payment_status] ?? $this->payment_status; }
    public function getPaymentMethodLabelAttribute(){ return self::PAYMENT_METHOD[$this->payment_method] ?? $this->payment_method; }

    // Quan hệ (nếu có bảng order_items; nếu chưa dùng có thể bỏ)
    public function items(){ return $this->hasMany(OrderItem::class,'order_id','order_id'); }

    // Lọc
    public function scopeFilter($q, array $f){
        return $q
            ->when(!empty($f['q']), function($qq) use($f){
                $kw = trim($f['q']);
                $qq->where(function($x) use($kw){
                    $x->where('order_code','like',"%$kw%")
                      ->orWhere('phone','like',"%$kw%")
                      ->orWhere('shipping_address','like',"%$kw%");
                });
            })
            ->when(isset($f['status']) && $f['status']!=='', fn($qq)=>$qq->where('status',$f['status']))
            ->when(isset($f['payment_status']) && $f['payment_status']!=='', fn($qq)=>$qq->where('payment_status',$f['payment_status']))
            ->when(!empty($f['date_from']), fn($qq)=>$qq->whereDate('created_at','>=',$f['date_from']))
            ->when(!empty($f['date_to']),   fn($qq)=>$qq->whereDate('created_at','<=',$f['date_to']));
    }
        public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

 
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'order_id','product_id','quantity',
        'price','discount_percent','discount_price',
    ];

    protected $casts = [
        'quantity'         => 'int',
        'price'            => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_price'   => 'decimal:2',
    ];

    public function order(){
        return $this->belongsTo(Order::class,'order_id','order_id');
    }

    // Nếu bán DỊCH VỤ (bảng services, PK = service_id) dùng quan hệ dưới:
      public function product() {
        // FK ở items: product_id, PK ở products: product_id
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    // Thuộc tính hiển thị gộp
    public function getDisplayNameAttribute() {
        return $this->product?->name
            ?? $this->product?->product_name
            ?? $this->service?->name
            ?? $this->service?->service_name
            ?? 'Mục #' . ($this->product_id ?? $this->service_id ?? $this->id);
    }

    public function getThumbPathAttribute() {
        return $this->product?->image_main
            ?? $this->product?->thumbnail
            ?? $this->service?->thumbnail
            ?? $this->service?->image
            ?? null;
    }
   

    // Đơn giá sau giảm
    public function getUnitFinalAttribute()
    {
        $u = (float) $this->price;
        if (!is_null($this->discount_price))  $u -= (float) $this->discount_price;
        if (!is_null($this->discount_percent) && (float)$this->discount_percent > 0){
            $u = $u * (1 - ((float)$this->discount_percent / 100));
        }
        return max($u, 0);
    }

    // Thành tiền = đơn giá sau giảm * SL
    public function getSubtotalAttribute()
    {
        return $this->unit_final * (int)$this->quantity;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';
    protected $primaryKey = 'service_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Các cột cho phép fill
    protected $fillable = [
        'service_name',
        'short_desc',
        'category_id',
        'type',            // 'single' | 'combo'
        'slug',
        'price',           // DECIMAL(10,2) - có thể bỏ nếu không dùng nữa
        'price_original',  // DECIMAL(12,0)
        'price_sale',      // DECIMAL(12,0) (nullable)
        'duration',        // phút
        'description',
        'images',
        'thumbnail',
        'status',          // 1=active, 0=inactive
        'is_featured',     // 1=nổi bật
    ];

    // Ép kiểu đúng với DB
    protected $casts = [
        'price'          => 'decimal:2',
        'price_original' => 'decimal:0',
        'price_sale'     => 'decimal:0',
        'duration'       => 'integer',
        'status'         => 'boolean',
        'is_featured'    => 'boolean',
        'category_id'    => 'string',
    ];

    /**
     * Dùng slug cho route model binding: /booking/{service:slug}
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Tự sinh slug nếu trống khi tạo mới
     */
    protected static function booted()
    {
        static::creating(function ($m) {
            if (empty($m->slug)) {
                $base = Str::slug($m->service_name);
                $m->slug = ($base !== '') ? $base.'-'.Str::random(4) : Str::random(8);
            }
        });
    }

    /**
     * Quan hệ: 1 dịch vụ thuộc 1 danh mục
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id', 'category_id');
    }
    public function appointments()
{
    return $this->hasMany(\App\Models\Appointment::class, 'service_id', 'service_id');
}

    /**
     * Giá cuối cùng để hiển thị (ưu tiên price_sale, sau đó price_original, cuối cùng price)
     */
    public function getFinalPriceAttribute()
    {
        return $this->price_sale ?? $this->price_original ?? $this->price;
    }

    /**
     * Format giá đẹp kèm "đ"
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        // Nếu đang dùng price_original/price_sale (VND không lẻ) => 0 chữ số thập phân
        $decimals = isset($this->attributes['price_sale']) || isset($this->attributes['price_original']) ? 0 : 2;
        return number_format((float) $this->final_price, $decimals, ',', '.').' đ';
    }

    /**
     * Scopes hay dùng
     */


    public function scopeFeatured($q)
    {
        return $q->active()->where('is_featured', 1);
    }
     public function scopeActive($q) {
        return $q->where('status', 1);
    }

    public function scopeSearch($q, $kw) {
        return $kw ? $q->where('service_name', 'like', '%'.$kw.'%') : $q;
    }

    public function scopePriceRange($q, $min = null, $max = null) {
        $expr = "CASE
            WHEN price IS NOT NULL AND price > 0 THEN price
            WHEN price_sale IS NOT NULL AND price_sale > 0 THEN price_sale
            ELSE price_original
        END";
        $q->select('*')->selectRaw("$expr AS effective_price");
        if ($min !== null && $min !== '') $q->whereRaw("$expr >= ?", [$min]);
        if ($max !== null && $max !== '') $q->whereRaw("$expr <= ?", [$max]);
        return $q;
    }

    public function getEffectivePriceAttribute() {
        if (!is_null($this->price) && $this->price > 0) return (float)$this->price;
        if (!is_null($this->price_sale) && $this->price_sale > 0) return (float)$this->price_sale;
        return (float)($this->price_original ?? 0);
    }
}

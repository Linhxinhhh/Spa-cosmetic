<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'products';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'product_id';

    /**
     * The attributes that are mass assignable.
     */
protected $fillable = [
    'product_name',
    'brand_id',
    'category_id',
    'price',
    'capacity',
    'variant_group',
    'discount_percent',
    'discount_price',
    'description',
    'specifications',
    'stock_quantity',
    'sold_quantity',
    'is_featured',
    'status'
];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'specifications' => 'array',
        
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship với brand
     */
    public function getStatusTextAttribute()
{
    return match ($this->status) {
        1 => 'Đang bán',
        2 => 'Ngưng bán',
        3 => 'Hết hàng',
        default => 'Không xác định',
    };
}
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Relationship với category
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }


    /**
     * Scope cho sản phẩm active
     */

public function scopeActive($q)
{
    return $q->where('status', 1); // 1 = Đang bán
}
    /**
     * Scope cho sản phẩm có giảm giá
     */
    public function scopeDiscounted($query)
    {
        return $query->whereNotNull('discount_price');
    }

    /**
     * Tính giá sau giảm giá
     */
    
    public function getDiscountPercentCalculatedAttribute()
{
    if ($this->discount_price && $this->price > 0) {
        return round((($this->price - $this->discount_price) / $this->price) * 100, 2);
    }
    return $this->discount_percent ?? 0;
}

// Thêm mutator
public function setDiscountPercentAttribute($value)
{
    $this->attributes['discount_percent'] = $value !== null ? max(0, min(100, $value)) : null;
}
    public function getIsNewAttribute()
    {
        return $this->created_at && $this->created_at->gt(now()->subDays(30));
    }
public function getImageUrlAttribute()
{
    // Lấy ảnh main trong bảng product_images
    $main = $this->mainImageRel()->first();

    if ($main) {
        return $main->url;
    }

    // Nếu chưa có ảnh main thì lấy ảnh đầu tiên trong imagesRel
    $first = $this->imagesRel()->first();
    return $first ? $first->url : null;
}

public function getThumbnailAttribute()
{
    return $this->image_url; // đã viết sẵn accessor image_url rồi
}

public function imagesRel()
{
    return $this->hasMany(ProductImage::class, 'product_id')
        ->orderByDesc('is_main')   // main trước
        ->orderBy('sort_order')    // phụ theo sort_order
        ->orderBy('id');
}

public function mainImageRel()
{
    return $this->hasOne(ProductImage::class, 'product_id', 'product_id')->where('is_main', 1);
}
   protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::makeUniqueSlug($product->product_name);
            }
        });

        static::updating(function ($product) {
            // Nếu muốn đổi slug khi đổi tên:
            if ($product->isDirty('product_name') && empty($product->slug)) {
                $product->slug = static::makeUniqueSlug($product->product_name);
            }
        });
    }

    public static function makeUniqueSlug(string $name): string
    {
        $base = Str::slug($name, '-', 'vi'); // hỗ trợ tiếng Việt
        $slug = $base ?: Str::random(8);

        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
public function getRouteKeyName() { return 'slug'; }
public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'product_id');
}

public function syncStatusByStock(): void
{
    // 1: đang bán, 2: ngừng bán, 3: hết hàng
    if ($this->stock_quantity <= 0) {
        $this->status = 3; // hết hàng
    } elseif ($this->status == 3) {
        $this->status = 1; // có hàng trở lại -> đang bán
    }
}
  // --- Scopes ---
    public function scopeKeyword($q, $kw){
        if(!$kw) return $q;
        return $q->where(function($qq) use($kw){
            $qq->where('product_name','like',"%$kw%")
               ->orWhere('slug','like',"%$kw%")
               ->orWhere('description','like',"%$kw%");
        });
    }

    public function scopeCategoryId($q, $id){
        if(!$id) return $q;
        return $q->where('category_id', $id);
    }

    public function scopeBrandId($q, $id){
        if(!$id) return $q;
        return $q->where('brand_id', $id);
    }

    public function scopePriceBetween($q, $min=null, $max=null){
        if(is_numeric($min)) $q->where('price','>=',(float)$min);
        if(is_numeric($max)) $q->where('price','<=',(float)$max);
        return $q;
    }

    public function scopeFeatured($q, $flag){
        if(!$flag) return $q;
        return $q->where('is_featured', 1);
    }

    public function scopeSortByKey($q, $sort){
        // map giá trị sort từ UI -> cột + hướng
        $map = [
            'newest' => ['created_at','desc'],
            'rating' => ['rating','desc'],          // nếu có cột rating
            'price_asc'  => ['price','asc'],
            'price_desc' => ['price','desc'],
            'popular' => ['sold_count','desc'],     // nếu có cột sold_count
            'default' => ['product_id','desc'],
        ];
        $key = $map[$sort]['0'] ?? $map['default'][0];
        $dir = $map[$sort]['1'] ?? $map['default'][1];
        return $q->orderBy($key, $dir);
    }
    public function capacityOptions(): array
{
    $arr = collect($this->capacities ?? [])
            ->filter()->map(fn($v) => trim((string)$v));

    if ($this->capacity) $arr->prepend(trim((string)$this->capacity));

    return $arr->unique()->values()->all();
}
// Các sản phẩm cùng nhóm (cùng model/mã nhóm)
public function scopeSameGroup($q, $group) {
    return $q->where('variant_group', $group);
}

public function siblings()
{
    return $this->hasMany(self::class, 'variant_group', 'variant_group')
        ->where('status', 1)
        ->where('category_id', $this->category_id)
        ->orderBy('capacity');
}

// Lấy 1 biến thể theo dung tích (kèm cùng category_id và status)
public function siblingByCapacity(string $capacity)
{
    return self::where('variant_group', $this->variant_group)
        ->where('category_id', $this->category_id)
        ->where('status', 1)
        ->where('capacity', $capacity)
        ->first();
}

// Danh sách “biến thể anh em” phục vụ view show (kể cả khi không có variant_group)
public function variantSiblings()
{
    return static::query()
        ->where('status', 1)
        ->where('category_id', $this->category_id)
        // CHỈ lấy danh mục con
        ->whereHas('category', fn($q) => $q->whereNotNull('parent_id'))
        // Cùng nhóm nếu có, không thì fallback theo tên sp
        ->when(
            filled($this->variant_group),
            fn($q) => $q->where('variant_group', $this->variant_group),
            fn($q) => $q->where('product_name', $this->product_name)
        )
        ->orderBy('capacity');
}
public function scopeSamePrefix($q, string $name, int $words = 5)
{
    $prefix = collect(explode(' ', $name))->take($words)->implode(' ');
    return $q->where('product_name','like',$prefix.'%');
}

}
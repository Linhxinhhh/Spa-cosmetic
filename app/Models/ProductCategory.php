<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';
    protected $primaryKey = 'category_id';
    public $timestamps = true;

    protected $fillable = [
        'category_name',
        'parent_id',
        'slug',
        'image',
        'description',
        'status', // 1 = hoạt động, 0 = ngưng hoạt động
    ];

    protected $casts = [
        'status'     => 'integer',   // lọc 0/1 chính xác
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ============== Relationships ============== */

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'category_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'category_id')->where('status', 1);
    }

    public function products()
    {
        // Nếu bảng products có cột category_id tham chiếu tới product_categories.category_id
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
        public function scopeParents($q) { return $q->whereNull('parent_id'); }
       public function scopeActive($q)     { return $q->where('status', 1); }
        public function scopeChildrenOnly($q){ return $q->whereNotNull('parent_id'); }
    /* ============== Scopes ============== */

    // Tìm theo tên CHÍNH XÁC
    public function scopeSearchName(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);
        return $term === '' ? $q : $q->where('category_name', $term);
    }

    // Tìm "mờ": tên/slug/ID
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') return $q;

        return $q->where(function ($w) use ($term) {
            $w->where('category_name', 'like', "%{$term}%")
              ->orWhere('slug', 'like', "%{$term}%")
              ->orWhere('category_id', $term);
        });
    }

    // Lọc trạng thái: nhận 1|0 hoặc 'hoạt động'/'ngưng hoạt động'
    public function scopeStatus(Builder $q, $status): Builder
    {
        if ($status === null || $status === '' || $status === 'all') return $q;

        $map = [
            'hoạt động'       => 1,
            'ngưng hoạt động' => 0,
            'active'          => 1,
            'inactive'        => 0,
            '1'               => 1,
            '0'               => 0,
        ];
        $key = is_string($status) ? mb_strtolower($status) : $status;
        $val = is_string($key) && array_key_exists($key, $map) ? $map[$key] : (int) $status;

        return $q->where('status', $val);
    }

    // level: '1' root | '2' child | 'all' bỏ qua
    public function scopeLevel(Builder $q, ?string $level): Builder
    {
        if (! $level || $level === 'all') return $q;
        if ($level === '1') return $q->whereNull('parent_id');
        if ($level === '2') return $q->whereNotNull('parent_id');
        return $q;
    }

    public function scopeCreatedBetween(Builder $q, ?string $from, ?string $to): Builder
    {
        return $q->when($from, fn ($qq) => $qq->whereDate('created_at', '>=', $from))
                 ->when($to,   fn ($qq) => $qq->whereDate('created_at', '<=', $to));
    }

    public function scopeRoot(Builder $q): Builder
    {
        return $q->whereNull('parent_id');
    }

    public function scopeNewest(Builder $q): Builder
    {
        return $q->orderBy('created_at', 'desc');
    }
}

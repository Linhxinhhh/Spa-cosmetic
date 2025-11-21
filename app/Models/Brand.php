<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
   use HasFactory;
    protected $primaryKey = 'brand_id';
    protected $fillable = [
        'brand_id',
        'brand_name',
        'logo',
        'description',
        'created_at',
        'status',
        'slug',
    ];
    public $timestamps = false;
    // Trong Model (Brand.php)
protected $time = [
    'created_at',
    'updated_at',
    'your_date_column'
];
// Trong Model
public function getYourDateColumnAttribute($value)
{
    return \Carbon\Carbon::parse($value);
}
protected $casts = [
    'logo' => 'string', // Đảm bảo logo được lưu dưới dạng chuỗi
];
// app/Models/Brand.php
public function products()
{
    return $this->hasMany(Product::class, 'brand_id', 'brand_id');
}
public function getLogoUrlAttribute()
{
    if (!$this->logo) {
        return asset('images/no-logo.png');
    }
    return asset('storage/' . ltrim($this->logo, '/'));
}

// Link tới trang sản phẩm theo brand
public function getLinkAttribute()
{
    return route('products.byBrand', $this->slug);
}
public function getRouteKeyName() { return 'slug'; }

}

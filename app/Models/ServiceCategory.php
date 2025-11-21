<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $table = 'service_categories';
    protected $primaryKey = 'category_id';
    public $incrementing = false; // vì tự tạo ID
    protected $keyType = 'string'; // nếu ID là chuỗi

    protected $fillable = [
        'category_id',
        'category_name',
        'parent_id',
        'description',
        'status',
        'created_at',
        'image'
    ];

    // Nếu bảng không có updated_at
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->category_id)) {
                // Ví dụ: ID dạng SV001
                $lastId = ServiceCategory::max('category_id');
                $number = $lastId ? (int)substr($lastId, 2) + 1 : 1;
                $model->category_id = 'SV' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id','category_id');
    }

    public function children()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id','category_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'category_id','category_id');
    }
      public function scopeParents($q)
    {
        return $q->whereNull('parent_id');
    }

}

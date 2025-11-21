<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guide extends Model
{
      protected $table = 'guides';
    protected $primaryKey = 'guide_id';
    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'summary',
        'content_html',
        'is_active',
        'category_id',
        'status',
        'published_at',
        'seo_title',
        'seo_description',  
    ];
     protected $casts = [
        'status' => 'integer',
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(GuideCategory::class, 'category_id');
    }
    
    public function tags()
    {
        return $this->belongsToMany(GuideTag::class, 'guide_tag_pivot', 'guide_id', 'tag_id');
    }
    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;

        return $q->where(function ($s) use ($term) {
            $s->where('title', 'like', "%{$term}%")
              ->orWhere('excerpt', 'like', "%{$term}%");
        });
    }
    public static function uniqueSlug(string $title): string
    {
        // 1. Tạo slug cơ bản
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        // 2. Kiểm tra xem slug đã tồn tại chưa
        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
    public function setGuideCategoryIdAttribute($value)
    {
        // Khi Laravel cố gắng lưu Guide, nó sẽ dùng giá trị này cho cột 'category_id'
        $this->attributes['category_id'] = $value;
    }
     public function getRouteKeyName(){ return 'slug'; }
     public function scopePublished($q)
{
    return $q->where('status', 1)->whereNotNull('published_at')->where('published_at', '<=', now());
}
}

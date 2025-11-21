<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $primaryKey = 'banner_id';
    protected $table = 'banners';
    public $timestamps = false;

    protected $fillable = [
        'title','image','link','position','status','start_date','end_date','created_at','updated_at'
    ];

    protected $casts = [
        'status'     => 'integer',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 1);
    }

    public function scopePosition(Builder $q, string $pos): Builder
    {
        return $q->where('position', $pos); // phải đúng y giá trị enum
    }

    public function scopeOnSchedule(Builder $q): Builder
    {
        $now = Carbon::now();
        return $q->where(function($qq) use ($now) {
                $qq->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })->where(function($qq) use ($now) {
                $qq->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });
    }

public function getImageUrlAttribute(): string
{
    $p = $this->image;

    if (!$p) {
        return asset('img/placeholder-1200x600.png');
    }

    // Full URL already?
    if (Str::startsWith($p, ['http://','https://','//'])) {
        return $p;
    }

    // Normalize and try storage/public
    $p = ltrim($p, '/');                     // remove leading slash
    $p = preg_replace('#^(storage/|public/)#', '', $p); // strip accidental prefixes

    // If file exists in storage/app/public/{path}
    if (Storage::disk('public')->exists($p)) {
        return Storage::url($p);             // => /storage/{path}
    }

    // Otherwise assume it lives directly under /public
    return asset($p);                        // => /{path}
}
}

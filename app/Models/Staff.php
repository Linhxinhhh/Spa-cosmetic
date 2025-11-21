<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $primaryKey = 'staff_id';

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'avatar',
        'position',
        'hire_date',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'status'    => 'boolean',
    ];

    /* ================== Relationships ================== */

    // Nếu staff có liên kết tài khoản user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // 1 nhân viên có nhiều cuộc hẹn
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'staff_id', 'staff_id');
    }

    /* ================== Accessors ================== */

    public function getAvatarUrlAttribute(): string
    {
        $avatar = $this->avatar;

        if (!$avatar) {
            return asset('images/no-avatar.png');
        }

        if (Str::startsWith($avatar, ['http://','https://','/'])) {
            return $avatar;
        }

        return Storage::disk('public')->exists($avatar)
            ? Storage::url($avatar)
            : asset('storage/'.$avatar);
    }

    /* ================== Scopes ================== */

    public function scopeActive($q)
    {
        return $q->where('status', 1);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';
    protected $primaryKey = 'appointment_id';

    protected $fillable = [
        'user_id','service_id','staff_id',
        'appointment_date','start_time','end_time',
        'status','notes'
    ];

    // CHỈ cast cho date; bỏ datetime cho các cột TIME
    protected $casts = [
        'appointment_date' => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    // ===== Accessor/Mutator cho TIME =====
    protected function startTime(): Attribute
    {
        return Attribute::make(
            // luôn trả về HH:MM để đổ vào <input type="time">
            get: fn ($value) => $value ? substr($value, 0, 5) : null,
            // khi gán từ form HH:MM -> lưu HH:MM:SS
            set: fn ($value) => $value
                ? (strlen($value) === 5 ? $value . ':00' : $value)
                : null
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? substr($value, 0, 5) : null,
            set: fn ($value) => $value
                ? (strlen($value) === 5 ? $value . ':00' : $value)
                : null
        );
    }

    // Trạng thái
    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // Quan hệ
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function service(): BelongsTo { return $this->belongsTo(Service::class, 'service_id'); }
    public function staff(): BelongsTo { return $this->belongsTo(User::class, 'staff_id'); }

    // Scope
    public function scopeUpcoming($query)
    {
        return $query->whereDate('appointment_date', '>=', now()->toDateString())
                     ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    // Hiển thị gộp ngày-giờ
    public function getFullDateTimeAttribute(): string
    {
        $date  = $this->appointment_date ? $this->appointment_date->format('d/m/Y') : '';
        $start = $this->start_time ?? '';
        $end   = $this->end_time ?? '';
        return trim("$date $start - $end");
    }
}

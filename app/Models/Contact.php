<?php

namespace App\Models;
use App\Models\Customer;
use App\Models\ContactReply;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // Bảng mặc định 'contacts' -> không cần $table
    // Khóa chính mặc định 'id', timestamps mặc định -> OK

    /** Trạng thái chuẩn */
    protected $table = 'contacts';
    protected $primaryKey = 'contact_id';
    public const STATUS_OPEN       = 'open';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE       = 'done';

    /** Cho phép gán hàng loạt */
    protected $fillable = [
        'customer_id',
        'name',
        'phone',
        'email',
        'subject',
        'message',
        'status',
        'source',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
         'responded_at' => 'datetime',
    ];

    /** Quan hệ: mỗi contact thuộc về 1 customer (có thể null) */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /** Scopes nhanh */
    public function scopeOpen($q)        { return $q->where('status', self::STATUS_OPEN); }
    public function scopeProcessing($q)  { return $q->where('status', self::STATUS_PROCESSING); }
    public function scopeDone($q)        { return $q->where('status', self::STATUS_DONE); }

    /** Accessor: nhãn trạng thái tiếng Việt */
    public function getStatusLabelAttribute(): string
    {
        return [
            self::STATUS_OPEN       => 'Mới',
            self::STATUS_PROCESSING => 'Đang xử lý',
            self::STATUS_DONE       => 'Hoàn tất',
        ][$this->status] ?? $this->status;
    }
     public function replies()
    {
        // FK trên contact_replies là 'contact_id' tham chiếu tới contacts.id
        return $this->hasMany(ContactReply::class, 'contact_id', 'id');
    }



}

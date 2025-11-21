<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use  App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
      public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
public function roles()
{
  return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
}
public function customer()
{
    return $this->hasOne(Customer::class, 'user_id', 'id');
}


public function hasRole($role)
{
    return $this->roles()->where('name', $role)->exists();
}
public function hasAnyRole(array $roles)
{
    return $this->roles()->whereIn('name', $roles)->exists();
}
public function assignRole($role)
{
    if(is_string($role)){
        $role = Role::where('name', $role)->firstOrFail();
    }
    $this->roles()->sync([$role->id]); // sync để chỉ có 1 role
}
protected $appends = ['avatar_url'];

public function getAvatarUrlAttribute(): string
    {
        $avatar = trim((string)($this->avatar ?? ''));
        if ($avatar === '') {
            return asset('admin/images/profile/user.png'); // fallback
        }
        // URL tuyệt đối hoặc path tuyệt đối thì dùng luôn
        if (Str::startsWith($avatar, ['http://','https://','/'])) {
            return $avatar;
        }
        // Còn lại: file trong public disk -> /storage/...
        return Storage::url($avatar); // nhớ chạy: php artisan storage:link
    }
   public function wishlist()
{
 return $this->belongsToMany(
        \App\Models\Product::class,
        'wishlist_items',   // TÊN BẢNG PIVOT
        'user_id',          // FK tới users
        'product_id',       // FK tới products
        'user_id',          // local key users
        'product_id'        // local key products
    )->withTimestamps();
}
 public function cart()
    {
        // foreign key trên bảng carts là 'user_id', local key trên users là 'user_id'
        return $this->hasOne(Cart::class, 'user_id', 'user_id');
    }
     public function orders()
    {
        // Nếu bảng orders có cột user_id trỏ đến users.user_id
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }
}

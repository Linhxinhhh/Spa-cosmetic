<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');  // Tạo cột user_id làm PRIMARY KEY (bigint unsigned auto-increment)
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Insert user mới (sau khi tạo schema)
        $user = User::create([
            'name' => 'Admin',
            'email' => 'Admin@gmail.com',  // Unique
            'phone' => '0123456789',
            'password' => Hash::make('Linh0107@'),
            'email_verified_at' => now(),  // Tùy chọn
        ]);

        // Gán role (insert vào pivot role_user)
        // Giả sử role_id = 1 ('admin') đã tồn tại từ migration roles trước
        // Kiểm tra tồn tại role VÀ bảng role_user để tránh lỗi foreign key / table not exist
        if (DB::table('roles')->where('role_id', 1)->exists() && Schema::hasTable('role_user')) {
            DB::table('role_user')->insertOrIgnore([
                [
                    'user_id' => $user->user_id,
                    'role_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // Hoặc dùng relationship (nếu Model đã setup roles())
        // $role = Role::find(1);
        // $user->roles()->attach($role->role_id);
    }

    public function down(): void
    {
        // Xóa data pivot trước khi drop bảng (dynamic bằng email)
        $userId = DB::table('users')->where('email', 'Admin@gmail.com')->value('user_id');
        if ($userId && Schema::hasTable('role_user')) {  // Kiểm tra bảng tồn tại
            DB::table('role_user')->where('user_id', $userId)->delete();
        }

        Schema::dropIfExists('users');
    }
};
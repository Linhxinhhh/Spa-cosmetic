<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;  // Để dùng DB::table và insert

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->primary(['user_id', 'role_id']);  // Composite primary key
            $table->foreign('user_id')->references('user_id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('role_id')->references('role_id')->on('roles')
                  ->onDelete('cascade');
        });

        // Insert data mẫu (sau khi tạo bảng)
        // Kiểm tra tồn tại để tránh lỗi foreign key (nếu users/roles chưa có data)
        $adminUserExists = DB::table('users')->where('user_id', 1)->exists();
        $adminRoleExists = DB::table('roles')->where('role_id', 1)->exists();
        $staffUserExists = DB::table('users')->where('user_id', 2)->exists();
        $staffRoleExists = DB::table('roles')->where('role_id', 2)->exists();

        $insertData = [];

        if ($adminUserExists && $adminRoleExists) {
            $insertData[] = [
                'user_id' => 1,
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($staffUserExists && $staffRoleExists) {
            $insertData[] = [
                'user_id' => 2,
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            DB::table('role_user')->insertOrIgnore($insertData);  // Bulk insert nếu có data
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa data cụ thể nếu rollback (tùy chọn, để clean)
        DB::table('role_user')->whereIn('user_id', [1, 2])->delete();
        Schema::dropIfExists('role_user');
    }
};
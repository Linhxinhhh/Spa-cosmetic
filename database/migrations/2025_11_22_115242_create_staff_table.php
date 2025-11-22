<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('staff_id');      // Khóa chính

            // Nếu bạn có bảng users, dùng user_id để liên kết tài khoản đăng nhập
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->string('full_name', 255);
            $table->string('email', 191)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('avatar', 255)->nullable();

            $table->string('position', 100)->nullable(); // chức vụ (lễ tân, kỹ thuật viên, quản lý,...)
            $table->date('hire_date')->nullable();        // ngày tuyển dụng

            $table->tinyInteger('status')->default(1);    // 1 = đang làm, 0 = đã nghỉ

            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};

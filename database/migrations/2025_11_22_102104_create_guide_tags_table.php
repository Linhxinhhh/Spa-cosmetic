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
        Schema::create('guide_tags', function (Blueprint $table) {
            // 1. tag_id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id('tag_id'); // $table->id() tạo cột 'tag_id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. name
            // Kiểu: varchar(255), Không NULL
            $table->string('name', 255);

            // 3. slug
            // Kiểu: varchar(120), Không NULL
            $table->string('slug', 120);

            // Tùy chọn: Thêm timestamps nếu cần thiết cho việc quản lý (mặc dù không có trong ảnh)
            // $table->timestamps();

            // Khai báo Indexes
            // PRIMARY KEY: tag_id (Đã tạo bởi $table->id('tag_id'))

            // UNIQUE Index: guide_tags_slug_unique
            $table->unique('slug', 'guide_tags_slug_unique');

            // Index: guide_tags_name_idx
            $table->index('name', 'guide_tags_name_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_tags');
    }
};
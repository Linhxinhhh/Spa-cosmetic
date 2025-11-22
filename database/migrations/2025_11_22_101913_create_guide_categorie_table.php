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
        Schema::create('guide_categories', function (Blueprint $table) {
            // 1. category_id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id('category_id'); // $table->id() tạo cột 'category_id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. name
            // Kiểu: varchar(255), Không NULL
            $table->string('name', 255);

            // 3. slug
            // Kiểu: varchar(255), Không NULL
            $table->string('slug', 255);

            // 4. description
            // Kiểu: text, Có thể NULL (NULL)
            $table->text('description')->nullable();

            // 5. created_at & 6. updated_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            // Vì ảnh hiển thị YES NULL cho created_at và updated_at, ta định nghĩa thủ công.
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Khai báo Indexes
            // PRIMARY KEY: category_id (Đã tạo bởi $table->id('category_id'))

            // UNIQUE Index: guide_categories_slug_unique
            $table->unique('slug', 'guide_categories_slug_unique');

            // Index: guide_categories_name_idx
            $table->index('name', 'guide_categories_name_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_categories');
    }
};
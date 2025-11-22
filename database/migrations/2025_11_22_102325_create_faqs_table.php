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
        Schema::create('faqs', function (Blueprint $table) {
            // 1. id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id(); // Laravel's id() mặc định tạo cột 'id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. contact_id (Liên kết với người/entiry nào đó đã đặt câu hỏi)
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('contact_id')->nullable();

            // 3. question
            // Kiểu: varchar(255), Không NULL
            $table->string('question', 255);

            // 4. answer
            // Kiểu: text, Không NULL
            $table->text('answer');

            // 5. category
            // Kiểu: varchar(100), Có thể NULL (NULL)
            $table->string('category', 100)->nullable();

            // 6. subcategory
            // Kiểu: varchar(100), Có thể NULL (NULL)
            $table->string('subcategory', 100)->nullable();

            // 7. cover_image
            // Kiểu: varchar(155), Có thể NULL (NULL)
            $table->string('cover_image', 155)->nullable();

            // 8. sort_order
            // Kiểu: int(10) UNSIGNED, Default: 0
            $table->unsignedInteger('sort_order')->default(0);

            // 9. status
            // Kiểu: enum('Nháp', 'Xuất bản'), Default: 'Xuất bản'
            $table->enum('status', ['Nháp', 'Xuất bản'])->default('Xuất bản');

            // 10. views
            // Kiểu: int(10) UNSIGNED, Default: 0
            $table->unsignedInteger('views')->default(0);

            // 11. created_at
            // Kiểu: datetime, Có thể NULL (NULL)
            $table->dateTime('created_at')->nullable();

            // 12. updated_at
            // Kiểu: datetime, Có thể NULL (NULL)
            $table->dateTime('updated_at')->nullable();

            // Khai báo Indexes
            // Index: idx_faqs_status
            $table->index('status', 'idx_faqs_status');

            // Index: idx_faqs_category
            $table->index('category', 'idx_faqs_category');

            // Index: idx_faqs_sort_order
            $table->index('sort_order', 'idx_faqs_sort_order');

            // Index: fk_contact_id
            $table->index('contact_id', 'fk_contact_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
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
        Schema::create('contact_replies', function (Blueprint $table) {
            // 1. id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id(); // Laravel's id() mặc định tạo cột 'id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. contact_id (Khóa ngoại tham chiếu đến bảng contacts)
            // Kiểu: bigint(20) UNSIGNED, Không NULL
            $table->unsignedBigInteger('contact_id');

            // 3. admin_id (Người phản hồi)
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('admin_id')->nullable();

            // 4. via (Kênh phản hồi)
            // Kiểu: enum('note', 'email', 'portal'), Default: 'portal', Không NULL
            $table->enum('via', ['note', 'email', 'portal'])->default('portal');

            // 5. message
            // Kiểu: text, Có thể NULL (NULL)
            $table->text('message')->nullable();

            // 6. created_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('created_at')->nullable();

            // 7. updated_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('updated_at')->nullable();

            // Khai báo Indexes
            // Index: contact_replies_contact_id_index
            $table->index('contact_id', 'contact_replies_contact_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_replies');
    }
};
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
        Schema::create('chat_sessions', function (Blueprint $table) {
            // 1. id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id(); // Laravel's id() mặc định tạo cột 'id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. user_id
            // Kiểu: bigint(20) UNSIGNED, Không NULL
            $table->unsignedBigInteger('user_id');

            // 3. title
            // Kiểu: varchar(255), Có thể NULL (NULL)
            $table->string('title', 255)->nullable();

            // 4. meta (Dữ liệu bổ sung, JSON/Text)
            // Kiểu: longtext, Có thể NULL (NULL)
            $table->longText('meta')->nullable();

            // 5. last_message_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('last_message_at')->nullable();

            // 6. created_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('created_at')->nullable();

            // 7. updated_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('updated_at')->nullable();

            // Khai báo Indexes
            // Index: chat_sessions_user_id_index
            $table->index('user_id', 'chat_sessions_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
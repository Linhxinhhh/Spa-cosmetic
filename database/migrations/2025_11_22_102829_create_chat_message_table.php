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
        Schema::create('chat_messages', function (Blueprint $table) {
            // 1. id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id();

            // 2. chat_session_id
            // Kiểu: bigint(20) UNSIGNED, Không NULL
            $table->unsignedBigInteger('chat_session_id');

            // 3. role
            // Kiểu: enum('system', 'user', 'assistant', 'tool'), Không NULL
            $table->enum('role', ['system', 'user', 'assistant', 'tool']);

            // 4. content
            // Kiểu: longtext, Không NULL
            $table->longText('content');

            // 5. tool_name
            // Kiểu: varchar(255), Có thể NULL (NULL)
            $table->string('tool_name', 255)->nullable();

            // 6. meta (Dữ liệu bổ sung, JSON/Text)
            // Kiểu: longtext, Có thể NULL (NULL)
            $table->longText('meta')->nullable();

            // 7. created_at
            // Kiểu: timestamp, Không NULL
            $table->timestamp('created_at')->nullable(false); // Default Laravel behavior, explicit for clarity

            // 8. updated_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('updated_at')->nullable();

            // Khai báo Indexes
            // Index: chat_messages_role_index
            $table->index('role', 'chat_messages_role_index');

            // Index: chat_messages_chat_session_id_index
            $table->index('chat_session_id', 'chat_messages_chat_session_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
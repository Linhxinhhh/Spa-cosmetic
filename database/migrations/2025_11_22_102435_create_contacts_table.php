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
        Schema::create('contacts', function (Blueprint $table) {
            // 1. contact_id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id('contact_id'); // $table->id() tạo cột 'contact_id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. customer_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('customer_id')->nullable();

            // 3. name
            // Kiểu: varchar(120), Không NULL
            $table->string('name', 120);

            // 4. phone
            // Kiểu: varchar(20), Có thể NULL (NULL)
            $table->string('phone', 20)->nullable();

            // 5. email
            // Kiểu: varchar(191), Có thể NULL (NULL)
            $table->string('email', 191)->nullable();

            // 6. subject
            // Kiểu: varchar(120), Có thể NULL (NULL)
            $table->string('subject', 120)->nullable();

            // 7. message
            // Kiểu: text, Không NULL
            $table->text('message');

            // 8. status
            // Kiểu: enum('open', 'processing', 'done'), Default: 'open'
            $table->enum('status', ['open', 'processing', 'done'])->default('open');

            // 9. responded_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('responded_at')->nullable();

            // 10. created_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('created_at')->nullable();

            // 11. updated_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('updated_at')->nullable();

            // Khai báo Indexes
            // Index: contacts_customer_id_index
            $table->index('customer_id', 'contacts_customer_id_index');

            // Index: contacts_phone_index
            $table->index('phone', 'contacts_phone_index');

            // Index: contacts_email_index
            $table->index('email', 'contacts_email_index');

            // Index: contacts_status_index
            $table->index('status', 'contacts_status_index');

            // Index: contacts_created_at_index
            $table->index('created_at', 'contacts_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
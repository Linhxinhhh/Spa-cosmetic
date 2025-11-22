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
        Schema::create('payments', function (Blueprint $table) {
            // 1. id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id(); // Laravel's id() mặc định tạo cột 'id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. provider
            // Kiểu: varchar(255), Không NULL
            $table->string('provider', 255);

            // 3. order_code
            // Kiểu: varchar(255), Có thể NULL (NULL)
            $table->string('order_code', 255)->nullable();

            // 4. order_id
            // Kiểu: bigint(20) UNSIGNED, Không NULL
            $table->unsignedBigInteger('order_id');

            // 5. amount
            // Kiểu: bigint(20) UNSIGNED, Không NULL
            $table->unsignedBigInteger('amount');

            // 6. currency
            // Kiểu: varchar(10), Default: VND, Không NULL
            $table->string('currency', 10)->default('VND');

            // 7. status
            // Kiểu: varchar(255), Default: pending, Không NULL
            $table->string('status', 255)->default('pending');

            // 8. txn_ref (Transaction Reference)
            // Kiểu: varchar(64), Có thể NULL (NULL)
            $table->string('txn_ref', 64)->nullable();

            // 9. request_payload
            // Kiểu: longtext, Có thể NULL (NULL)
            $table->longText('request_payload')->nullable();

            // 10. response_payload
            // Kiểu: longtext, Có thể NULL (NULL)
            $table->longText('response_payload')->nullable();

            // 11. callback_payload
            // Kiểu: longtext, Có thể NULL (NULL)
            $table->longText('callback_payload')->nullable();

            // 12 & 13. created_at & updated_at
            // Kiểu: timestamp, Có thể NULL (NULL) - Lưu ý: trong ảnh là YES NULL
            // Mặc định $table->timestamps() là NOT NULL, ta cần chỉnh sửa.
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            // Đảm bảo sử dụng Schema và DB facade
        });

        // Khai báo Indexes sau khi tạo bảng
        Schema::table('payments', function (Blueprint $table) {
            // Index: idx_payments_txn_ref
            $table->index('txn_ref', 'idx_payments_txn_ref');
        });

        // Khóa ngoại: fk_order_id
        // $table->foreign('order_id', 'fk_order_id')->references('id')->on('orders')->onDelete('cascade');
        // Tôi sẽ không thêm foreign key constraint trừ khi bạn yêu cầu, vì cấu trúc chỉ hiển thị Index.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
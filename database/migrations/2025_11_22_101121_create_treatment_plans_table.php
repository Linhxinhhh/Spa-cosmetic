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
        Schema::create('treatment_plans', function (Blueprint $table) {
            // 1. id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id(); // Laravel's id() mặc định tạo cột 'id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. customer_id
            // Kiểu: bigint(20) UNSIGNED, Không NULL
            $table->unsignedBigInteger('customer_id');

            // 3. package_service_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('package_service_id')->nullable();

            // 4. single_service_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('single_service_id')->nullable();

            // 5. start_date
            // Kiểu: date, Không NULL
            $table->date('start_date');

            // 6. preferred_dow (Day of Week - Ngày trong tuần ưu tiên)
            // Kiểu: longtext, Có thể NULL (NULL)
            $table->longText('preferred_dow')->nullable();

            // 7. preferred_time_range
            // Kiểu: longtext, Có thể NULL (NULL)
            $table->longText('preferred_time_range')->nullable();

            // 8. branch_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('branch_id')->nullable();

            // 9. staff_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('staff_id')->nullable();

            // 10. room_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('room_id')->nullable();

            // 11. status
            // Kiểu: enum('draft', 'active', 'scheduled', 'confirmed', 'completed', 'cancelled')
            // Default: 'draft'
            $table->enum('status', ['draft', 'active', 'scheduled', 'confirmed', 'completed', 'cancelled'])->default('draft');

            // 12. note
            // Kiểu: text, Có thể NULL (NULL)
            $table->text('note')->nullable();

            // 13 & 14. created_at & updated_at
            // Kiểu: timestamp
            $table->timestamps();

            // Khai báo Indexes
            // Index: idx_customer_status
            $table->index(['customer_id', 'status'], 'idx_customer_status');

            // Index: idx_pkg_service
            $table->index('package_service_id', 'idx_pkg_service');

            // Index: idx_single_service
            $table->index('single_service_id', 'idx_single_service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_plans');
    }
};
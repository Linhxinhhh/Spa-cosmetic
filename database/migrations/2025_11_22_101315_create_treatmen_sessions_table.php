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
        Schema::create('treatment_sessions', function (Blueprint $table) {
            // 1. id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id(); // Laravel's id() mặc định tạo cột 'id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. treatment_plan_id
            // Kiểu: bigint(20) UNSIGNED, Không NULL
            $table->unsignedBigInteger('treatment_plan_id');

            // 3. session_no
            // Kiểu: smallint(5) UNSIGNED, Không NULL
            $table->unsignedSmallInteger('session_no');

            // 4. package_step_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('package_step_id')->nullable();

            // 5. scheduled_start
            // Kiểu: datetime, Không NULL
            $table->dateTime('scheduled_start');

            // 6. scheduled_end
            // Kiểu: datetime, Không NULL
            $table->dateTime('scheduled_end');

            // 7. staff_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('staff_id')->nullable();

            // 8. room_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('room_id')->nullable();

            // 9. status
            // Kiểu: enum('draft', 'scheduled', 'confirmed', 'completed', 'cancelled')
            // Default: 'draft'
            $table->enum('status', ['draft', 'scheduled', 'confirmed', 'completed', 'cancelled'])->default('draft');

            // 10. checkin_at
            // Kiểu: datetime, Có thể NULL (NULL)
            $table->dateTime('checkin_at')->nullable();

            // 11. checkout_at
            // Kiểu: datetime, Có thể NULL (NULL)
            $table->dateTime('checkout_at')->nullable();

            // 12. note
            // Kiểu: text, Có thể NULL (NULL)
            $table->text('note')->nullable();

            // 13 & 14. created_at & updated_at
            // Kiểu: timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_sessions');
    }
};
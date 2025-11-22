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
        Schema::create('service_package_steps', function (Blueprint $table) {
            // 1. id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id(); // Laravel's id() mặc định tạo cột 'id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. package_service_id (Khóa ngoại)
            // Kiểu: bigint(20) UNSIGNED
            $table->unsignedBigInteger('package_service_id');
            // Cột này có thể là khóa ngoại tham chiếu đến bảng 'package_service_package_meta'
            // $table->foreign('package_service_id')->references('package_service_id')->on('package_service_package_meta')->onDelete('cascade');
            // Tôi sẽ không thêm foreign key constraint trừ khi bạn yêu cầu, vì cấu trúc chỉ hiển thị UNIQUE Index.

            // 3. step_no
            // Kiểu: smallint(5) UNSIGNED, Không NULL
            $table->unsignedSmallInteger('step_no');

            // 4. child_service_id
            // Kiểu: bigint(20) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedBigInteger('child_service_id')->nullable();

            // 5. title
            // Kiểu: varchar(255), Có thể NULL (NULL)
            $table->string('title', 255)->nullable();

            // 6. duration_min
            // Kiểu: smallint(5) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedSmallInteger('duration_min')->nullable();

            // 7. min_gap_days
            // Kiểu: smallint(5) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedSmallInteger('min_gap_days')->nullable();

            // 8. max_gap_days
            // Kiểu: smallint(5) UNSIGNED, Có thể NULL (NULL)
            $table->unsignedSmallInteger('max_gap_days')->nullable();

            // 9. notes
            // Kiểu: text, Có thể NULL (NULL)
            $table->text('notes')->nullable();

            // 10 & 11. created_at & updated_at
            // Kiểu: timestamp
            $table->timestamps();

            // Khai báo Index
            // Index UNIQUE: uci_pkg_step (package_service_id, step_no)
            $table->unique(['package_service_id', 'step_no'], 'uci_pkg_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_package_steps');
    }
};
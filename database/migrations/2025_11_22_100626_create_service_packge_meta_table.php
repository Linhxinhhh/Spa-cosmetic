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
        Schema::create('service_package_meta', function (Blueprint $table) {
            // Cột khóa chính: package_service_id
            // Kiểu: bigint(20) UNSIGNED, KHÔNG NULL, KHÔNG Default, Tự tăng (Auto Increment)
            $table->id('package_service_id'); // $table->id() mặc định là unsignedBigInteger và tự tăng.

            // total_sessions
            // Kiểu: smallint(5) UNSIGNED, Default: 1
            $table->unsignedSmallInteger('total_sessions')->default(1);

            // default_duration_min
            // Kiểu: smallint(5) UNSIGNED, Default: 60
            $table->unsignedSmallInteger('default_duration_min')->default(60);

            // min_gap_days
            // Kiểu: smallint(5) UNSIGNED, Default: 0
            $table->unsignedSmallInteger('min_gap_days')->default(0);

            // max_gap_days
            // Kiểu: smallint(5) UNSIGNED, Default: 365
            $table->unsignedSmallInteger('max_gap_days')->default(365);

            // allowed_dow (Day of Week - Ngày trong tuần được phép)
            // Kiểu: longtext, Collation: utf8mb4_bin, NULL
            // Lưu ý: Cột này có Collation đặc biệt, thường dùng để lưu JSON hoặc chuỗi serialized
            // Tuy nhiên, trong migration, ta chỉ cần định nghĩa kiểu dữ liệu.
            $table->longText('allowed_dow')->nullable();

            // active
            // Kiểu: tinyint(1), Default: 1
            $table->tinyInteger('active')->default(1);

            // created_at & updated_at
            // Kiểu: timestamp, Default: current_timestamp(), ON UPDATE current_timestamp()
            // Laravel's $table->timestamps() sẽ xử lý logic này.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_service_package_meta');
    }
};
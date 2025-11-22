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
        Schema::create('banners', function (Blueprint $table) {
            // 1. banner_id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id('banner_id');

            // 2. title
            // Kiểu: varchar(255), Không NULL
            $table->string('title', 255);

            // 3. image
            // Kiểu: varchar(255), Có thể NULL (NULL)
            $table->string('image', 255)->nullable();

            // 4. link
            // Kiểu: varchar(512), Có thể NULL (NULL)
            $table->string('link', 512)->nullable();

            // 5. position
            // Kiểu: varchar(50), Không NULL
            $table->string('position', 50);

            // 6. status
            // Kiểu: tinyint(4), Default: 1
            $table->tinyInteger('status')->default(1);

            // 7. start_date
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('start_date')->nullable();

            // 8. end_date
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('end_date')->nullable();

            // 9. created_at & 10. updated_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Khai báo Indexes
            
            // Index: banners_position_index
            $table->index(['position', 'status'], 'banners_position_index');

            // Index: banners_status_index
            $table->index('status', 'banners_status_index');

            // Index: banners_start_date_index
            $table->index('start_date', 'banners_start_date_index');

            // Index: banners_end_date_index
            $table->index('end_date', 'banners_end_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
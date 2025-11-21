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
            $table->engine = 'InnoDB';

            $table->bigIncrements('banner_id');          // PK

            $table->string('title', 255);
            $table->string('image', 255)->nullable();
            $table->string('link', 512)->nullable();

            // position: tuỳ bạn muốn enum hay text. Để linh hoạt, dùng string + index
            // (nếu bạn có danh sách cố định như: home_top, home_mid, sidebar..., có thể chuyển sang enum)
            $table->string('position', 50)->index();

            $table->tinyInteger('status')->default(1)->index();  // 1=active, 0=inactive

            // Lịch hiển thị
            $table->timestamp('start_date')->nullable()->index();
            $table->timestamp('end_date')->nullable()->index();

            // Model không dùng $timestamps nên tạo thủ công:
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
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

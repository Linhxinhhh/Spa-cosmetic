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
        Schema::create('service_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            // PK là chuỗi tự sinh kiểu "SV001"
            $table->string('category_id', 10)->primary();

            $table->string('category_name', 255);
            // self-reference: cùng kiểu string, cho phép null
            $table->string('parent_id', 10)->nullable()->index();

            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('slug', 255);
            // 1 = hoạt động, 0 = ngưng
            $table->tinyInteger('status')->default(1);

            // chỉ có created_at, không có updated_at
            $table->timestamp('created_at')->useCurrent();

            // FK tự tham chiếu, xóa cha -> parent_id = null
            $table->foreign('parent_id')
                  ->references('category_id')
                  ->on('service_categories')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};

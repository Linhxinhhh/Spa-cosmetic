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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('category_id');      // PK khớp model

            $table->string('category_name', 255);
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->string('slug', 255)->unique();
            $table->string('image', 255)->nullable();
            $table->text('description')->nullable();

            $table->tinyInteger('status')->default(1); // 1 = hoạt động, 0 = ngưng

            $table->timestamps();

            // Quan hệ cha — tự tham chiếu
            $table->foreign('parent_id')
                  ->references('category_id')
                  ->on('product_categories')
                  ->nullOnDelete();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};

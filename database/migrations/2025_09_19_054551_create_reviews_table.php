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
        Schema::create('reviews', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('review_id');

            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('service_id')->nullable()->index();

            $table->unsignedTinyInteger('rating'); // 1–5 sao
            $table->text('comment')->nullable();
            $table->json('images')->nullable();    // Lưu danh sách ảnh (nếu có)

            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('product_id')->references('product_id')->on('products')->nullOnDelete();
            $table->foreign('service_id')->references('service_id')->on('services')->nullOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

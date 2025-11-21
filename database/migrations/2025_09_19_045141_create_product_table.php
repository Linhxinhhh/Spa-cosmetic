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
        Schema::create('products', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('product_id');     // PK khớp với model

            $table->string('product_name', 255);
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();

            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();

            $table->longText('description')->nullable();
            $table->json('specifications')->nullable();

            $table->integer('stock_quantity')->default(0);
            $table->integer('sold_quantity')->default(0);

            $table->boolean('is_featured')->default(false);
            $table->tinyInteger('status')->default(1); // 1=Đang bán, 2=Ngưng, 3=Hết hàng

            $table->string('slug', 255)->unique();

            $table->timestamps();

            // Khóa ngoại (tùy bạn đã có bảng chưa)
            $table->foreign('brand_id')->references('brand_id')->on('brands')->nullOnDelete();
            $table->foreign('category_id')->references('category_id')->on('product_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};

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
        Schema::create('order_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('item_id');                   // PK
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->index();

            $table->integer('quantity')->default(1);

            $table->decimal('price', 12, 2);
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('discount_price', 12, 2)->nullable();

            $table->timestamps();

            $table->foreign('order_id')
                  ->references('order_id')->on('orders')
                  ->cascadeOnDelete();

            $table->foreign('product_id')
                  ->references('product_id')->on('products')
                  ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

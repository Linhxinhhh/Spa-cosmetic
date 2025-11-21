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
        Schema::create('orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('order_id');             // PK
            $table->unsignedBigInteger('user_id')->index();

            $table->string('order_code')->unique();
            $table->decimal('total_amount', 12, 2)->default(0);

            $table->enum('payment_method', ['cod','momo','vnpay'])->default('cod');
            $table->enum('payment_status', ['pending','paid','failed'])->default('pending');

            $table->enum('status', ['pending','processing','shipped','delivered'])->default('pending');

            $table->string('shipping_address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('note')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('user_id')->on('users')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

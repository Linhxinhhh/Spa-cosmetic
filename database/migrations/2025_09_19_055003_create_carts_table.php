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
        Schema::create('carts', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('cart_id');           // PK
            $table->unsignedBigInteger('user_id')->index()->nullable();

            // Model $fillable có 'created_at' nên ta vẫn dùng timestamps
            $table->timestamps();                        // created_at, updated_at

            $table->foreign('user_id')
                  ->references('user_id')->on('users')
                  ->nullOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};

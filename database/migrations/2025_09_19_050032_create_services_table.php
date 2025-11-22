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
        Schema::create('services', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('service_id'); // PK int tự tăng

            $table->string('service_name', 255);
            $table->string('short_desc', 255)->nullable();

            // FK -> service_categories.category_id (kiểu string)
            $table->string('category_id', 10)->nullable()->index();

            $table->enum('type', ['single', 'combo'])->default('single');

            $table->string('slug', 255)->unique();

            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('price_original', 12, 0)->nullable();
            $table->decimal('price_sale', 12, 0)->nullable();

            $table->integer('duration')->nullable();      // phút
            $table->longText('description')->nullable();

            $table->json('images')->nullable();
            $table->string('thumbnail', 255)->nullable();

            $table->boolean('status')->default(1);        
            $table->tinyInteger('is_active')->nullable();
            $table->boolean('is_featured')->default(0);   // 1=nổi bật

            $table->timestamps();

            $table->foreign('category_id')
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
        Schema::dropIfExists('services');
    }
};

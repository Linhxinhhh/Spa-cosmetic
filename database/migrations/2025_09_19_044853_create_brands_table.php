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
        Schema::create('brands', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('brand_id');         // PK

            $table->string('brand_name', 255);
            $table->string('logo', 255)->nullable();
            $table->text('description')->nullable();

            $table->timestamp('created_at')->useCurrent(); // vì $timestamps = false
            $table->tinyInteger('status')->default(1);

            $table->string('slug', 255)->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};

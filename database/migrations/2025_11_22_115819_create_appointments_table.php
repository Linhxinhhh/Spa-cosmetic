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
        Schema::create('appointments', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('appointment_id');

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('service_id')->nullable()->index();
            $table->unsignedBigInteger('staff_id')->nullable()->index();

            $table->date('appointment_date')->index();
            $table->time('start_time');
            $table->time('end_time');

            $table->enum('status', ['pending','confirmed','completed','cancelled'])
                  ->default('pending')
                  ->index();

            $table->text('notes')->nullable();

            $table->timestamps();

            // FK
            $table->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();
            $table->foreign('service_id')->references('service_id')->on('services')->nullOnDelete();
            $table->foreign('staff_id')->references('staff_id')->on('staff')->nullOnDelete();

            // Gợi ý: tránh trùng slot cho cùng nhân viên (không chặn overlap hoàn toàn,
            // nhưng ngăn trùng hệt khoảng thời gian)
            $table->unique(['staff_id', 'appointment_date', 'start_time', 'end_time'], 'uniq_staff_date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

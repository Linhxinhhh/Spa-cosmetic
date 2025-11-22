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
        Schema::create('roles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('role_id');
            $table->string('name', 100)->unique();
            $table->string('display_name', 150)->nullable();
            $table->timestamps();
        });
        DB::table('roles')->insert([
        [
            'name' => 'admin',
            'display_name' => 'Quản Trị Viên',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'staff',
            'display_name' => 'Nhân Viên',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

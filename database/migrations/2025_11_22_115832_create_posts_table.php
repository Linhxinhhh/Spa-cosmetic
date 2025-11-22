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
        Schema::create('posts', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('post_id');

            $table->string('title');
            $table->string('slug')->unique();

            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();

            $table->unsignedBigInteger('author_id')->index();
            $table->enum('status', ['draft','published','archive'])->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();

            $table->timestamps();

            $table->foreign('author_id')
                  ->references('user_id')->on('users')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

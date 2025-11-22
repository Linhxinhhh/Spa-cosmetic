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
        Schema::create('guides', function (Blueprint $table) {
            // 1. guide_id (Khóa chính)
            // Kiểu: bigint(20) UNSIGNED, Tự tăng (AUTO_INCREMENT)
            $table->id('guide_id'); // $table->id() tạo cột 'guide_id' là BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // 2. title
            // Kiểu: varchar(255), Không NULL
            $table->string('title', 255);

            // 3. slug
            // Kiểu: varchar(255), Không NULL
            $table->string('slug', 255);

            // 4. thumbnail
            // Kiểu: varchar(255), Có thể NULL (NULL)
            $table->string('thumbnail', 255)->nullable();

            // 5. seo_title
            // Kiểu: varchar(255), Có thể NULL (NULL)
            $table->string('seo_title', 255)->nullable();

            // 6. seo_description
            // Kiểu: varchar(255), Có thể NULL (NULL)
            $table->string('seo_description', 255)->nullable();

            // 7. category_id
            // Kiểu: bigint(20) UNSIGNED, Không NULL
            $table->unsignedBigInteger('category_id');

            // 8. excerpt
            // Kiểu: text, Có thể NULL (NULL)
            $table->text('excerpt')->nullable();

            // 9. content_html
            // Kiểu: longtext, Không NULL
            $table->longText('content_html');

            // 10. status
            // Kiểu: tinyint(1) UNSIGNED, Default: 0
            $table->unsignedTinyInteger('status')->default(0);

            // 11. views
            // Kiểu: int(10) UNSIGNED, Default: 0
            $table->unsignedInteger('views')->default(0);

            // 12. published_at
            // Kiểu: datetime, Có thể NULL (NULL)
            $table->dateTime('published_at')->nullable();

            // 13. created_at & 14. updated_at
            // Kiểu: timestamp, Có thể NULL (NULL)
            // Sử dụng $table->timestamps() sau đó chỉnh sửa nullable
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Khai báo Indexes
            // PRIMARY KEY: guide_id (Đã tạo bởi $table->id('guide_id'))

            // UNIQUE Index: guides_slug_unique
            $table->unique('slug', 'guides_slug_unique');

            // Index: guides_category_idx
            $table->index('category_id', 'guides_category_idx');

            // Index: guides_status_idx
            $table->index('status', 'guides_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
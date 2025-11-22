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
        Schema::create('wishlist_items', function (Blueprint $table) {
            // Khóa chính (Mã ID của mục trong danh sách)
            $table->id();

            // 1. Khóa ngoại tới bảng users (Người tạo danh sách)
            // Phải dùng unsignedBigInteger để khớp với $table->id() trong bảng users
            $table->unsignedBigInteger('user_id');

            // 2. Khóa ngoại tới bảng products (Sản phẩm được thêm)
            // Phải dùng unsignedBigInteger để khớp với $table->id() trong bảng products
            $table->unsignedBigInteger('product_id');

            // Timestamps
            $table->timestamps();
            
            // --- Tối ưu hóa và Ràng buộc ---

            // 3. Ràng buộc Duy nhất (Unique Constraint/Index)
            // Đảm bảo một người dùng chỉ có thể thêm một sản phẩm một lần.
            // Đây cũng là index tối ưu cho việc truy vấn.
            $table->unique(['user_id', 'product_id'], 'uci_wishlist_user_product');
            
            // 4. Ràng buộc Khóa ngoại (Foreign Keys)
            
            // Kết nối với bảng users
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade'); 

            // Kết nối với bảng products
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};
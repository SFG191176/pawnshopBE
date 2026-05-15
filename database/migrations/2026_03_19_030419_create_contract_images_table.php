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
        Schema::create('contract_images', function (Blueprint $table) {
            $table->id();

            // 1. Khóa ngoại liên kết chặt chẽ với bảng contracts
            // (onDelete('cascade') nghĩa là: Nếu Admin xóa hợp đồng, toàn bộ ảnh của nó cũng tự động bốc hơi theo)
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');

            // 2. Đường dẫn lưu ảnh (vd: /uploads/xe-vision-1.jpg)
            $table->string('image_url');

            // 3. Đánh dấu ảnh bìa (Ảnh chính hiện ra đầu tiên)
            $table->boolean('is_main')->default(false)->comment('Xác định đây là ảnh bìa');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_images');
    }
};

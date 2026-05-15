<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Thêm 4 cột mới, cho phép rỗng (nullable) để không bị lỗi với các hợp đồng cũ
            $table->string('customer_id_date')->nullable()->after('customer_id_card'); // Ngày cấp
            $table->string('customer_id_place')->nullable()->after('customer_id_date'); // Nơi cấp
            $table->string('customer_address')->nullable()->after('customer_id_place'); // Hộ khẩu
            $table->string('asset_condition')->nullable()->after('asset_description'); // Tình trạng tài sản
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['customer_id_date', 'customer_id_place', 'customer_address', 'asset_condition']);
        });
    }
};

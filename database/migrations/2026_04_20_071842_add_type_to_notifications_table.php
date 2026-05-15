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
        Schema::table('notifications', function (Blueprint $table) {
            // Thêm cột 'type' để phân loại thông báo (info, reminder, warning)
            // Cột này sẽ nằm ngay sau cột 'title'
            $table->string('type')->default('info')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Xóa cột 'type' nếu bạn muốn hoàn tác (rollback)
            $table->dropColumn('type');
        });
    }
};

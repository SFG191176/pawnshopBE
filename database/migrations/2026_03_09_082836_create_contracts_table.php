<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_id_card')->nullable();

            $table->string('asset_name');
            $table->text('asset_description')->nullable();
            $table->string('appraised_value')->nullable();

            $table->string('loan_amount');
            $table->float('interest_rate')->default(0);
            $table->integer('cycle_days')->default(30);
            $table->date('loan_date')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->string('paid_interest')->default('0');

            $table->string('sale_price')->nullable();// Cột giá niêm yết
            $table->string('actual_sold_price')->nullable()->comment('Giá chốt bán thực tế');
            $table->string('image')->nullable();

            $table->string('status')->default('Đang cầm');
            $table->string('status_color')->default('bg-green-100 text-green-700');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

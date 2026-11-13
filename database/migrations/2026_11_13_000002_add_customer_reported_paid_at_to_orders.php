<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('customer_reported_paid_at')
                ->nullable()
                ->after('payment_status')
                ->comment('Khách bấm "Tôi đã chuyển khoản" - chờ shop xác nhận');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('customer_reported_paid_at');
        });
    }
};

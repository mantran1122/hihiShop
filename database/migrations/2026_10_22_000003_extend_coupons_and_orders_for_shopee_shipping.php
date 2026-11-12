<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('coupons', function (Blueprint $t) {
            if (!Schema::hasColumn('coupons', 'category')) {
                $t->string('category', 30)->default('order_discount')->after('type'); // order_discount, free_shipping
            }
            if (!Schema::hasColumn('coupons', 'title')) {
                $t->string('title')->nullable()->after('code');
            }
            if (!Schema::hasColumn('coupons', 'description')) {
                $t->text('description')->nullable()->after('title');
            }
        });

        Schema::table('orders', function (Blueprint $t) {
            if (!Schema::hasColumn('orders', 'coupon_code')) {
                $t->string('coupon_code')->nullable()->after('discount_total');
            }
            if (!Schema::hasColumn('orders', 'shipping_coupon_code')) {
                $t->string('shipping_coupon_code')->nullable()->after('coupon_code');
            }
            if (!Schema::hasColumn('orders', 'shipping_discount')) {
                $t->decimal('shipping_discount', 12, 2)->default(0)->after('shipping_total');
            }
            if (!Schema::hasColumn('orders', 'shipping_carrier')) {
                $t->string('shipping_carrier', 50)->nullable()->after('shipping_status');
            }
            if (!Schema::hasColumn('orders', 'shipping_service')) {
                $t->string('shipping_service', 50)->nullable()->after('shipping_carrier');
            }
        });

        Schema::table('carts', function (Blueprint $t) {
            if (!Schema::hasColumn('carts', 'coupon_code')) {
                $t->string('coupon_code')->nullable()->after('discount_total');
            }
            if (!Schema::hasColumn('carts', 'shipping_coupon_code')) {
                $t->string('shipping_coupon_code')->nullable()->after('coupon_code');
            }
            if (!Schema::hasColumn('carts', 'shipping_discount')) {
                $t->decimal('shipping_discount', 12, 2)->default(0)->after('shipping_total');
            }
            if (!Schema::hasColumn('carts', 'shipping_carrier')) {
                $t->string('shipping_carrier', 50)->nullable()->after('shipping_discount');
            }
            if (!Schema::hasColumn('carts', 'shipping_service')) {
                $t->string('shipping_service', 50)->nullable()->after('shipping_carrier');
            }
        });
    }

    public function down(): void {
        Schema::table('carts', function (Blueprint $t) {
            $t->dropColumn(['coupon_code', 'shipping_coupon_code', 'shipping_discount', 'shipping_carrier', 'shipping_service']);
        });

        Schema::table('orders', function (Blueprint $t) {
            $t->dropColumn(['coupon_code', 'shipping_coupon_code', 'shipping_discount', 'shipping_carrier', 'shipping_service']);
        });

        Schema::table('coupons', function (Blueprint $t) {
            $t->dropColumn(['category', 'title', 'description']);
        });
    }
};


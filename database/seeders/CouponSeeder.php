<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            // 1. Nhóm Voucher Vận chuyển (Freeship Shopee Xtra)
            [
                'code' => 'FREESHIP15',
                'title' => 'Freeship Xtra Giảm 15K',
                'description' => 'Giảm 15.000đ phí vận chuyển cho đơn hàng từ 200.000đ',
                'category' => 'free_shipping',
                'type' => 'fixed',
                'value' => 15000,
                'min_order' => 200000,
                'max_discount' => 15000,
                'usage_limit' => 1000,
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP30',
                'title' => 'Freeship Xtra Giảm 30K',
                'description' => 'Giảm 30.000đ phí vận chuyển cho đơn hàng từ 350.000đ',
                'category' => 'free_shipping',
                'type' => 'fixed',
                'value' => 30000,
                'min_order' => 350000,
                'max_discount' => 30000,
                'usage_limit' => 1000,
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIPMAX',
                'title' => 'Freeship Toàn Quốc 100%',
                'description' => 'Miễn phí vận chuyển 100% (tối đa 50.000đ) cho đơn từ 500.000đ',
                'category' => 'free_shipping',
                'type' => 'percent',
                'value' => 100,
                'min_order' => 500000,
                'max_discount' => 50000,
                'usage_limit' => 500,
                'is_active' => true,
            ],

            // 2. Nhóm Voucher Giảm giá của Shop (Shop Voucher)
            [
                'code' => 'HIHI10',
                'title' => 'Giảm 10% Tối Đa 50K',
                'description' => 'Giảm 10% giá trị đơn hàng, tối đa 50.000đ cho đơn từ 250.000đ',
                'category' => 'order_discount',
                'type' => 'percent',
                'value' => 10,
                'min_order' => 250000,
                'max_discount' => 50000,
                'usage_limit' => 1000,
                'is_active' => true,
            ],
            [
                'code' => 'HIHI30K',
                'title' => 'Giảm Trực Tiếp 30.000đ',
                'description' => 'Giảm ngay 30.000đ vào tổng tiền hàng cho đơn từ 300.000đ',
                'category' => 'order_discount',
                'type' => 'fixed',
                'value' => 30000,
                'min_order' => 300000,
                'max_discount' => 30000,
                'usage_limit' => 500,
                'is_active' => true,
            ],
            [
                'code' => 'HIHI50K',
                'title' => 'Giảm Trực Tiếp 50.000đ',
                'description' => 'Giảm ngay 50.000đ vào tổng tiền hàng cho đơn từ 500.000đ',
                'category' => 'order_discount',
                'type' => 'fixed',
                'value' => 50000,
                'min_order' => 500000,
                'max_discount' => 50000,
                'usage_limit' => 300,
                'is_active' => true,
            ],
            [
                'code' => 'WELCOME20',
                'title' => 'Chào Bạn Mới Giảm 20K',
                'description' => 'Mã ưu đãi tri ân khách hàng mới cho đơn từ 150.000đ (mỗi khách 1 lần)',
                'category' => 'order_discount',
                'type' => 'fixed',
                'value' => 20000,
                'min_order' => 150000,
                'max_discount' => 20000,
                'usage_limit' => 2000,
                'per_user_limit' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $v) {
            Coupon::updateOrCreate(['code' => $v['code']], $v);
        }
    }
}


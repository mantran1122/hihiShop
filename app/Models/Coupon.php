<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'title',
        'description',
        'type',
        'category',
        'value',
        'min_order',
        'max_discount',
        'usage_limit',
        'used',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Kiểm tra tính hợp lệ của voucher
     */
    public function isValidFor($subtotal, $shippingFee = 0)
    {
        if (!$this->is_active) {
            return [false, 'Mã giảm giá này hiện đang tạm ngưng.'];
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return [false, 'Mã giảm giá chưa đến đợt sử dụng.'];
        }

        if ($this->ends_at && now()->gt($this->ends_at)) {
            return [false, 'Mã giảm giá đã hết hạn sử dụng.'];
        }

        if ($this->usage_limit !== null && $this->used >= $this->usage_limit) {
            return [false, 'Mã giảm giá đã đạt giới hạn lượt sử dụng.'];
        }

        if ($subtotal < $this->min_order) {
            return [false, 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($this->min_order, 0, '.', '.') . 'đ để dùng mã này.'];
        }

        return [true, 'Hợp lệ'];
    }

    /**
     * Tính toán số tiền được giảm
     */
    public function calculateDiscount($subtotal, $shippingFee = 0)
    {
        if ($this->category === 'free_shipping') {
            // Giảm phí vận chuyển
            if ($this->type === 'percent') {
                $discount = ($shippingFee * $this->value) / 100;
            } else {
                $discount = $this->value;
            }

            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }

            return min($discount, $shippingFee);
        }

        // Giảm giá tiền hàng
        if ($this->type === 'percent') {
            $discount = ($subtotal * $this->value) / 100;
        } else {
            $discount = $this->value;
        }

        if ($this->max_discount !== null && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return min($discount, $subtotal);
    }
}


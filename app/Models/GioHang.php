<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GioHang extends Model
{
    protected $table = 'carts';
    protected $fillable = [
        'user_id','session_id','currency',
        'subtotal','discount_total','coupon_code','shipping_coupon_code',
        'shipping_total','shipping_discount','shipping_carrier','shipping_service',
        'grand_total'
    ];

    public function mucs(){ return $this->hasMany(MucGioHang::class, 'cart_id'); }
    public function items(){ return $this->hasMany(MucGioHang::class, 'cart_id'); }

    public function getItemsAttribute()
    {
        return $this->mucs;
    }

    public static function for($user, $sessionId)
    {
        if ($user) {
            $cart = static::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'session_id' => $sessionId ?: Str::uuid()->toString(),
                    'currency' => 'VND',
                    'subtotal' => 0,
                    'discount_total' => 0,
                    'shipping_total' => 0,
                    'grand_total' => 0
                ]
            );

            // Hợp nhất món hàng từ giỏ khách vãng lai nếu có
            if ($sessionId) {
                $guestCart = static::whereNull('user_id')->where('session_id', $sessionId)->first();
                if ($guestCart && $guestCart->id !== $cart->id) {
                    foreach ($guestCart->mucs as $guestItem) {
                        $existing = $cart->mucs()->where('variant_id', $guestItem->variant_id)->first();
                        if ($existing) {
                            $existing->qty += $guestItem->qty;
                            $existing->line_total = $existing->qty * $existing->unit_price;
                            $existing->save();
                            $guestItem->delete();
                        } else {
                            $guestItem->cart_id = $cart->id;
                            $guestItem->save();
                        }
                    }
                    $guestCart->delete();
                    $cart->recalc();
                }
            }

            return $cart;
        }

        // Dành cho khách vãng lai
        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
        }

        return static::firstOrCreate(
            ['user_id' => null, 'session_id' => $sessionId],
            [
                'currency' => 'VND',
                'subtotal' => 0,
                'discount_total' => 0,
                'shipping_total' => 0,
                'grand_total' => 0
            ]
        );
    }

    public function recalc()
    {
        $sub = $this->mucs()->sum('line_total') ?: 0;
        $this->subtotal = $sub;
        $shippingNet = max(0, ($this->shipping_total ?: 0) - ($this->shipping_discount ?: 0));
        $this->grand_total = max(0, $sub - ($this->discount_total ?: 0) + $shippingNet);
        $this->save();
    }
}

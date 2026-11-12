<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'user_id','code','status','cancel_reason','admin_note','payment_status','shipping_status',
        'shipping_carrier','shipping_service',
        'shipping_address_id','billing_address_id','currency',
        'subtotal','discount_total','coupon_code','shipping_coupon_code',
        'shipping_total','shipping_discount','grand_total'
    ];

    public function mucs(){ return $this->hasMany(MucDonHang::class, 'order_id'); }
    public function items(){ return $this->hasMany(MucDonHang::class, 'order_id'); }
    public function user(){ return $this->belongsTo(User::class, 'user_id'); }
    public function diaChi(){ return $this->belongsTo(DiaChi::class, 'shipping_address_id'); }
    public function payments(){ return $this->hasMany(ThanhToan::class, 'order_id'); }
    public function payment(){ return $this->hasOne(ThanhToan::class, 'order_id')->latestOfMany(); }
    public function shipments(){ return $this->hasMany(VanChuyen::class, 'order_id'); }
    public function shipment(){ return $this->hasOne(VanChuyen::class, 'order_id')->latestOfMany(); }
}

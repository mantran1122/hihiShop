<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MucDonHang extends Model
{
    protected $table = 'order_items';
    protected $fillable = ['order_id','product_id','variant_id','sku','name_snapshot','price','qty','subtotal'];

    public function donHang(){ return $this->belongsTo(DonHang::class, 'order_id'); }
    public function sanPham(){ return $this->belongsTo(SanPham::class, 'product_id'); }
    public function bienThe(){ return $this->belongsTo(BienTheSanPham::class, 'variant_id'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MucGioHang extends Model
{
    protected $table = 'cart_items';
    protected $fillable = ['cart_id','product_id','variant_id','qty','unit_price','line_total'];

    public function gioHang(){ return $this->belongsTo(GioHang::class, 'cart_id'); }
    public function sanPham(){ return $this->belongsTo(SanPham::class, 'product_id'); }
    public function bienThe(){ return $this->belongsTo(BienTheSanPham::class, 'variant_id'); }
}

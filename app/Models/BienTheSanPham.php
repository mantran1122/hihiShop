<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BienTheSanPham extends Model
{
    protected $table = 'product_variants';
    protected $fillable = ['product_id','sku','price','compare_price','cost','stock_qty','is_default','weight'];

    public function sanPham(){ return $this->belongsTo(SanPham::class, 'product_id'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnhSanPham extends Model
{
    protected $table = 'product_images';
    protected $fillable = ['product_id','variant_id','path','is_cover','sort'];

    public function sanPham(){ return $this->belongsTo(SanPham::class, 'product_id'); }
    public function bienThe(){ return $this->belongsTo(BienTheSanPham::class, 'variant_id'); }
}

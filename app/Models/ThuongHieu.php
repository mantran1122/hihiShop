<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThuongHieu extends Model
{
    protected $table = 'brands';
    protected $fillable = ['name','slug','origin'];

    public function sanPhams(){ return $this->hasMany(SanPham::class, 'brand_id'); }
}

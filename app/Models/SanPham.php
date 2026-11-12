<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'products';
    protected $fillable = ['category_id','brand_id','name','slug','short_desc','description','is_active','seo_title','seo_description'];

    public function danhMuc(){ return $this->belongsTo(DanhMuc::class, 'category_id'); }
    public function thuongHieu(){ return $this->belongsTo(ThuongHieu::class, 'brand_id'); }
    public function bienThes(){ return $this->hasMany(BienTheSanPham::class, 'product_id'); }
    public function anh(){ return $this->hasMany(AnhSanPham::class, 'product_id'); }
    public function images(){ return $this->anh(); }

    public function bienTheMacDinh(){
        return $this->hasOne(BienTheSanPham::class, 'product_id')->where('is_default', true);
    }

    public function scopeActive($q){ return $q->where('is_active', true); }

    public function getPriceAttribute()
    {
        return $this->bienTheMacDinh?->price ?? $this->bienThes?->first()?->price ?? 0;
    }

    public function getImageUrlAttribute()
    {
        $cover = $this->anh?->where('is_cover', true)->first() ?? $this->anh?->first();
        if ($cover && $cover->path) {
            return asset($cover->path);
        }
        return asset('images/placeholder.svg');
    }
}


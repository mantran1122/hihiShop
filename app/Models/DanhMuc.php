<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $table = 'categories';
    protected $fillable = ['parent_id','name','slug','description','position','is_active'];

    public function cha(){ return $this->belongsTo(DanhMuc::class, 'parent_id'); }
    public function con(){ return $this->hasMany(DanhMuc::class, 'parent_id'); }
    public function sanPhams(){ return $this->hasMany(SanPham::class, 'category_id'); }

    public function scopeActive($q){ return $q->where('is_active', true); }

    public function getImageUrlAttribute()
    {
        // Ưu tiên quan hệ đã eager load để tránh N+1 query
        $prod = $this->relationLoaded('sanPhams')
            ? $this->sanPhams->first()
            : $this->sanPhams()->with('anh')->first();

        return $prod?->image_url ?? asset('images/placeholder.svg');
    }
}

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
    public function reviews(){ return $this->hasMany(Review::class, 'product_id'); }
    public function approvedReviews(){ return $this->reviews()->where('is_approved', true); }
    public function wishlists(){ return $this->hasMany(Wishlist::class, 'product_id'); }

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

    /**
     * Điểm đánh giá trung bình (1 chữ số thập phân), chỉ tính review đã duyệt.
     */
    public function getAverageRatingAttribute(): float
    {
        $avg = $this->relationLoaded('approvedReviews')
            ? $this->approvedReviews->avg('rating')
            : $this->approvedReviews()->avg('rating');

        return round((float) $avg, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->relationLoaded('approvedReviews')
            ? $this->approvedReviews->count()
            : $this->approvedReviews()->count();
    }

    /**
     * Sản phẩm đã được khách đang đăng nhập thả tim hay chưa.
     */
    public function isWishedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->wishlists()->where('user_id', $user->id)->exists();
    }
}


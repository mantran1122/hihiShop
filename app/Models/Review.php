<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'content',
        'images',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'is_approved' => 'boolean',
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Số sao trong khoảng 1..5 kể cả khi dữ liệu bị sai.
     */
    public function getStarsAttribute(): int
    {
        return max(1, min(5, (int) $this->rating));
    }
}

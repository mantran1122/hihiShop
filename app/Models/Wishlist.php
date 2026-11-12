<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'wishlists';

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

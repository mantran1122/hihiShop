<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaChi extends Model
{
    protected $table = 'addresses';
    protected $fillable = [
        'user_id', 'name', 'phone', 'province', 'district', 'ward', 'address_line', 'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'provider',
        'transaction_id',
        'status',
        'amount',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'decimal:2'
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'order_id');
    }
}

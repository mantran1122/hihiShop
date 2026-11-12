<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VanChuyen extends Model
{
    protected $table = 'shipments';

    protected $fillable = [
        'order_id',
        'carrier',
        'service',
        'tracking_code',
        'status',
        'fee',
        'label_url'
    ];

    protected $casts = [
        'fee' => 'decimal:2'
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'order_id');
    }
}


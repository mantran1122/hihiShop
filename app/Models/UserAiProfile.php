<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAiProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_categories',
        'preferred_brands',
        'budget_range',
        'style_persona',
        'interaction_count',
        'last_analyzed_at',
    ];

    protected $casts = [
        'preferred_categories' => 'array',
        'preferred_brands' => 'array',
        'interaction_count' => 'integer',
        'last_analyzed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


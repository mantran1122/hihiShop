<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiTrainingPair extends Model
{
    use HasFactory;

    protected $fillable = [
        'prompt',
        'prompt_hash',
        'ideal_response',
        'context_json',
        'source',
        'hits',
        'helpful_count',
        'unhelpful_count',
        'quality_score',
        'is_active',
        'is_exported',
    ];

    protected $casts = [
        'context_json' => 'array',
        'hits' => 'integer',
        'helpful_count' => 'integer',
        'unhelpful_count' => 'integer',
        'quality_score' => 'float',
        'is_active' => 'boolean',
        'is_exported' => 'boolean',
    ];
}


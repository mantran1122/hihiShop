<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiTrainingPair extends Model
{
    use HasFactory;

    protected $fillable = [
        'prompt',
        'ideal_response',
        'context_json',
        'source',
        'is_exported',
    ];

    protected $casts = [
        'context_json' => 'array',
        'is_exported' => 'boolean',
    ];
}


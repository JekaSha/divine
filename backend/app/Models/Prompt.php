<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $fillable = ['template', 'ai_model', 'ai_type', 'stream'];

    protected $casts = [
        'stream' => 'array', // Автоматическая обработка JSON
    ];
}

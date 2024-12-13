<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['requests', 'price', 'currency', 'stream', 'status'];

    protected $casts = [
        'stream' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    use HasFactory;

    protected $table = 'strategies';

    protected $fillable = [
        'name',
        'status',
        'stream',
        'className',
    ];

    protected $casts = [
        'stream' => 'json',
    ];


}

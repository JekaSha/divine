<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProp extends Model
{
    protected $fillable = ['user_id', 'name', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Связь с пользователем.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

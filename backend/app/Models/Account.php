<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exchange_id',
        'name',
        'description',
        'api_key',
        'api_secret',
        'status',
    ];

    /**
     * Связь с пользователем.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с биржей.
     */
    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }

    /**
     * Связь с кошельками.
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }
}

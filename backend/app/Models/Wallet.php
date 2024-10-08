<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'currency_id',
        'protocol_id',
        'wallet_token',
        'status',
        'user_id',
    ];

    /**
     * Связь с аккаунтом.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**

     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**

     */
    public function protocol()
    {
        return $this->belongsTo(CurrencyProtocol::class);
    }

    /**
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

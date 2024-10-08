<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountExchange extends Model
{
    use HasFactory;

    /**
     *
     * @var string
     */
    protected $table = 'account_exchange';

    /**
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'exchange_id',
        'status',
    ];


    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
}

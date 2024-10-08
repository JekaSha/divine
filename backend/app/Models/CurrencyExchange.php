<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyExchange extends Model
{
    use HasFactory;

    protected $table = 'currency_exchange';

    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'exchange_id',
        'current_rate',
    ];

    /**
     * Связь с валютой, из которой осуществляется обмен.
     */
    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    /**
     * Связь с валютой, на которую осуществляется обмен.
     */
    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    /**
     * Связь с биржей.
     */
    public function exchange()
    {
        return $this->belongsTo(Exchange::class, 'exchange_id');
    }
}

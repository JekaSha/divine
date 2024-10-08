<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyProtocol extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * Связь с валютами через таблицу currency_protocol.
     */
    public function currencies()
    {
        return $this->belongsToMany(Currency::class, 'currency_protocol', 'protocol_id', 'currency_id');
    }
}

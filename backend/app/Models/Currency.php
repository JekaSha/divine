<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * Связь с протоколами через таблицу currency_protocol.
     */
    public function protocols()
    {
        return $this->belongsToMany(CurrencyProtocol::class, 'currency_protocols', 'currency_id', 'protocol_id');

    }




}

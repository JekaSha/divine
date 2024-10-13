<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'status',
        'user_id',
        'amount',
        'wallet_address',
        'currency_id',
        'protocol_id',
        'current_rate',
        'stream',
        'comment',
        'email',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stream' => 'array',
        'amount' => 'decimal:8',
        'current_rate' => 'decimal:8',
    ];

    /**
     * Get the transaction associated with the order.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the currency associated with the order.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the protocol associated with the order.
     */
    public function protocol()
    {
        return $this->belongsTo(CurrencyProtocol::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Define fillable attributes
    protected $fillable = [
        'wallet_id',
        'type',
        'status',
        'amount',
        'exchange_rate',
        'expiry_time',
        'stream',
    ];

    // Define relationships

    /**
     * Get the wallet associated with the transaction.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the order associated with the transaction.
     */
    public function order()
    {
        return $this->belongsToMany(Order::class, 'order_transaction')
            ->using(OrderTransaction::class)
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_transaction')
            ->withTimestamps();
    }


}

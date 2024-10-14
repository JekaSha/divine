<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

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
        'hash',
    ];

    protected $casts = [
        'stream' => 'array',
        'amount' => 'decimal:8',
        'current_rate' => 'decimal:8',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->hash = $order->generateHash();

            if (static::where('hash', $order->hash)->exists()) {

                $existingOrder = static::where('hash', $order->hash)->first();

                throw new \Exception('An identical order already exists. Order ID: ' . $existingOrder->id);
            }
        });
    }


    private function generateHash()
    {
        $roundedTimestamp = $this->getRoundedTimestamp(5);
        return md5($this->user_id . $this->wallet_address . $this->currency_id . $this->amount . $roundedTimestamp);
    }

    private function getRoundedTimestamp($minutes)
    {
        $timestamp = now()->timestamp;
        return floor($timestamp / ($minutes * 60)) * ($minutes * 60);
    }

    public static function createOrFetch($data)
    {
        $hash = md5($data['user_id'] . $data['wallet_address'] . $data['currency_id'] . $data['amount'] . (now()->timestamp - now()->timestamp % (5 * 60)));

        $existingOrder = self::where('hash', $hash)->first();

        if ($existingOrder) {
            return $existingOrder;
        }

        return self::create($data);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function protocol()
    {
        return $this->belongsTo(CurrencyProtocol::class);
    }
}

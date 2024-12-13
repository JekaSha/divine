<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = ['user_id', 'total', 'currency', 'packages', 'hash', 'status'];

    protected $casts = [
        'packages' => 'array',
        'stream' => 'array',
        'response' => 'array'
    ];

    /**
     * The "booted" method of the model.
     * Automatically generate a hash when creating an invoice.
     */
    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->hash)) {
                $invoice->hash = Str::uuid()->toString(); // Generate a unique UUID
            }
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}

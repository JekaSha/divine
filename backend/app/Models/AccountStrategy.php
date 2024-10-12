<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AccountStrategy extends Pivot
{
    use HasFactory;

    protected $table = 'account_strategy'; // Specify the pivot table name

    protected $fillable = [
        'account_id',
        'strategy_id',
    ];

    // Define relationships

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }
}

<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FundsCredited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $eventType;

    public function __construct(Transaction $transaction, $eventType = 'FundsCredited')
    {
        $this->transaction = $transaction;
        $this->eventType = $eventType;
    }
}

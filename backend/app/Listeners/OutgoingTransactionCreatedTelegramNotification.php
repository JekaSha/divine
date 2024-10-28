<?php

namespace App\Listeners;

use App\Events\OutgoingTransactionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramNotifier; // Импорт трейта
use Illuminate\Support\Facades\Log;

class OutgoingTransactionCreatedTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue, TelegramNotifier;

    /**
     *
     * @param  \App\Events\OutgoingTransactionCreated  $event
     * @return void
     */
    public function handle(OutgoingTransactionCreated $event)
    {
        Log::debug('OutgoingTransactionCreated listener triggered.');

        $transaction = $event->transaction;
        $this->account = $transaction->wallet->account;
        $message = "OUTGOING Transaction {$transaction->id} created";

        $this->sendTelegramMessage($message);
    }
}

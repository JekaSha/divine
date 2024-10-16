<?php

namespace App\Listeners;

use App\Events\IncomingTransactionStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramNotifier; // Импорт трейта
use Illuminate\Support\Facades\Log;

class OutgoingTransactionStatusUpdateTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue, TelegramNotifier;

    /**
     *
     * @param  \App\Events\OutgoingTransactionStatusUpdated  $event
     * @return void
     */
    public function handle(OutgoingTransactionStatusUpdated $event)
    {
        Log::debug('IncomingTransactionStatusUpdateTelegramNotification listener triggered.');

        $transaction = $event->transaction;
        $message = "OUTGOING Transaction {$transaction->id} status updated to {$transaction->status}";

        $this->sendTelegramMessage($message);
    }
}

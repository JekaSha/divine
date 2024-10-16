<?php

namespace App\Listeners;

use App\Events\IncomingTransactionStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramNotifier; // Импорт трейта
use Illuminate\Support\Facades\Log;

class IncomingTransactionStatusUpdateTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue, TelegramNotifier;

    /**
     *
     * @param  \App\Events\IncomingTransactionStatusUpdated  $event
     * @return void
     */
    public function handle(IncomingTransactionStatusUpdated $event)
    {
        Log::debug('IncomingTransactionStatusUpdateTelegramNotification listener triggered.');

        $transaction = $event->transaction;
        $message = "INCOMING Transaction {$transaction->id} status updated to {$transaction->status}";

        $this->sendTelegramMessage($message); // Используем метод из трейта
    }
}

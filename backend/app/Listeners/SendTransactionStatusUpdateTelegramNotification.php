<?php

namespace App\Listeners;

use App\Events\TransactionStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramNotifier; // Импорт трейта
use Illuminate\Support\Facades\Log;

class SendTransactionStatusUpdateTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue, TelegramNotifier;

    /**
     *
     * @param  \App\Events\TransactionStatusUpdated  $event
     * @return void
     */
    public function handle(TransactionStatusUpdated $event)
    {
        Log::debug('SendTransactionStatusUpdateTelegramNotification listener triggered.');

        $transaction = $event->transaction;
        $message = "Transaction {$transaction->id} status updated to {$transaction->status}";

        $this->sendTelegramMessage($message); // Используем метод из трейта
    }
}

<?php

namespace App\Listeners;

use App\Events\OutgoingTransactionStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramNotifier; // Импорт трейта
use Illuminate\Support\Facades\Log;

class FundsDebited implements ShouldQueue
{
    use InteractsWithQueue, TelegramNotifier;

    /**
     *
     * @param  \App\Events\OutgoingTransactionStatusUpdated  $event
     * @return void
     */
    public function handle(FundsDebited $event)
    {
        Log::debug('FundsDebited listener triggered.');

        $transaction = $event->transaction;
        $message = "OUTGOING Transaction  {$transaction->id} sent to address: {$transaction->wallet->wallet_token}";

        $this->sendTelegramMessage($message);
    }
}

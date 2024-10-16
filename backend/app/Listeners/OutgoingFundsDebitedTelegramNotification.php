<?php

namespace App\Listeners;

use App\Events\FundsDebited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramNotifier;
use Illuminate\Support\Facades\Log;

class OutgoingFundsDebitedTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue, TelegramNotifier;

    /**
     *
     * @param  \App\Events\FundsDebited  $event
     * @return void
     */
    public function handle(FundsDebited $event)
    {
        Log::debug('IncomingFundsCreditedTelegramNotification listener triggered.');

        $transaction = $event->transaction;
        $message = "Funds Debited to transaction {$transaction->id}. Amount: {$transaction->amount}";

        $this->sendTelegramMessage($message); //use method from trait
    }
}

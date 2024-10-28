<?php

namespace App\Listeners;

use App\Events\FundsCredited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramNotifier;
use Illuminate\Support\Facades\Log;

class IncomingFundsCreditedTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue, TelegramNotifier;

    /**
     *
     * @param  \App\Events\FundsCredited  $event
     * @return void
     */
    public function handle(FundsCredited $event)
    {
        Log::debug('IncomingFundsCreditedTelegramNotification listener triggered.');

        $transaction = $event->transaction;
        $this->account = $transaction->wallet->account;
        $message = "Funds credited to transaction {$transaction->id}. Amount: {$transaction->amount}";


        $this->sendTelegramMessage($message); //use method from trait
    }
}

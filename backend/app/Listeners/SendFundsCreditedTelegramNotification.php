<?php

namespace App\Listeners;

use App\Events\FundsCredited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramNotifier;
use Illuminate\Support\Facades\Log;

class SendFundsCreditedTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue, TelegramNotifier;

    /**
     *
     * @param  \App\Events\FundsCredited  $event
     * @return void
     */
    public function handle(FundsCredited $event)
    {
        Log::debug('SendFundsCreditedTelegramNotification listener triggered.');

        $transaction = $event->transaction;
        $message = "Funds credited to transaction {$transaction->id}. Amount: {$transaction->amount}";

        $this->sendTelegramMessage($message); //use method from trait
    }
}

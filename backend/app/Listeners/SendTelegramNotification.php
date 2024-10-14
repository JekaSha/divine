<?php

namespace App\Listeners;

use App\Events\TransactionStatusUpdated;
use App\Events\FundsCredited; // Include any new events you want to handle
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class SendTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event)
    {
        // Check the type of event and call the appropriate method
        if ($event instanceof TransactionStatusUpdated) {
            $this->sendStatusUpdateMessage($event);
        } elseif ($event instanceof FundsCredited) {
            $this->sendFundsCreditedMessage($event);
        }
    }

    protected function sendStatusUpdateMessage(TransactionStatusUpdated $event)
    {
        $transaction = $event->transaction;
        $message = "Transaction {$transaction->id} status updated to {$transaction->status}";

        $this->sendTelegramMessage($message);
    }

    protected function sendFundsCreditedMessage(FundsCredited $event)
    {
        $transaction = $event->transaction;
        $message = "Funds credited to transaction {$transaction->id}. Amount: {$transaction->amount}";

        $this->sendTelegramMessage($message);
    }

    protected function sendTelegramMessage($message)
    {
        Http::post("https://api.telegram.org/bot777987349:AAHrIYeWTKib6Q8ZxfrRy9om5V8estHD7-g/sendMessage", [
            'chat_id' => '183396872',
            'text' => $message,
        ]);
    }
}

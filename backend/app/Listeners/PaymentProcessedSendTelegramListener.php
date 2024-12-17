<?php
namespace App\Listeners;

use App\Events\PaymentProcessed;
use Illuminate\Support\Facades\Http;

class PaymentProcessedSendTelegramListener
{
    /**
     * Handle the event.
     *
     * @param PaymentProcessed $event
     * @return void
     */
    public function handle(PaymentProcessed $event)
    {
        $invoice = $event->invoice;
        $status = $invoice->status;
        $invoiceHash = $invoice->hash ?? 'unknown';

        $telegramToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if ($telegramToken && $chatId) {
            $message = "Payment status: *$status*\nInvoice: *$invoiceHash*";
bb($message);
            Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        }
    }
}

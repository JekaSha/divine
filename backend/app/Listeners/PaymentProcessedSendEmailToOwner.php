<?php

namespace App\Listeners;

use App\Events\PaymentProcessed;
use Illuminate\Support\Facades\Mail;

class PaymentProcessedSendEmailToOwner
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

        $recipient = env('EMAIL_NOTIFICATION');

        if ($recipient) {
            $subject = "Payment Status Update";
            $body = "Payment for invoice {$invoiceHash} has been processed with status: {$status}.";

            Mail::raw($body, function ($message) use ($recipient, $subject) {
                $message->to($recipient)
                    ->subject($subject);
            });
        }
    }
}


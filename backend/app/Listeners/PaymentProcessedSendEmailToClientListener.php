<?php

namespace App\Listeners;

use App\Events\PaymentProcessed;
use Illuminate\Support\Facades\Mail;

class PaymentProcessedSendEmailToClientListener
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

        if ($invoice && $invoice->user && $invoice->user->email) {
            $recipient = $invoice->user->email;
            $subject = "Your Payment Status";
            $body = "Dear customer, the payment for your invoice {$invoiceHash} has been processed with status: {$status}.";

            Mail::raw($body, function ($message) use ($recipient, $subject) {
                $message->to($recipient)
                    ->subject($subject);
            });
        }
    }
}

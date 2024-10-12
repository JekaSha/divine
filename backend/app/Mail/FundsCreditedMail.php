<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FundsCreditedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function build()
    {
        return $this->subject('Funds Credited Notification')
            ->view('emails.funds_credited') // Ensure this view exists
            ->with([
                'transactionId' => $this->transaction->id,
                'amount' => $this->transaction->amount,
                'status' => $this->transaction->status,
            ]);
    }
}

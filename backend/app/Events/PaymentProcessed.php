<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentProcessed
{
    use Dispatchable, SerializesModels;

    public $invoice;


    /**
     * Create a new event instance.
     *
     * @param object $paymentIntent
     * @param string $status
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;

    }
}

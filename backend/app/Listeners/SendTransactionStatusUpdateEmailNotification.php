<?php

namespace App\Listeners;

use App\Events\TransactionStatusUpdated;
use App\Events\FundsCredited; // Import the new event
use App\Repositories\OrderRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionStatusUpdatedMail;
use App\Mail\FundsCreditedMail; // Import the new mail class
use App\Models\Order;

class SendTransactionStatusUpdateEmailNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $orderRepository;
    public function __construct(
        OrderRepository $orderRepository
    ) {

        $this->orderRepository = $orderRepository;
    }
    public function handle(TransactionStatusUpdated $event)
    {

        $this->sendStatusUpdateEmail($event);

    }

    protected function sendStatusUpdateEmail(TransactionStatusUpdated $event)
    {
        $transaction = $event->transaction;

        // Find the order by transaction ID
        $order = $this->orderRepository->get(['transaction_id' => $transaction->id])->first();

        // Send email to the order's email address if available
        if ($order && $order->email) {
            Mail::to($order->email)->send(new TransactionStatusUpdatedMail($transaction));
        }

        // Send email to the user associated with the transaction
        $userEmail = $transaction->wallet->user->email ?? null; // Get the user's email from the wallet

        if ($userEmail) {
            Mail::to($userEmail)->send(new TransactionStatusUpdatedMail($transaction));
        }
    }
}

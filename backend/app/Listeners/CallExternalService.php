<?php

namespace App\Listeners;

use App\Events\TransactionStatusUpdated;
use App\Events\FundsCredited; // Include the FundsCredited event
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class CallExternalService implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event)
    {
        // Check the type of event and call the appropriate method
        if ($event instanceof TransactionStatusUpdated) {
            $this->updateTransactionStatus($event);
        } elseif ($event instanceof FundsCredited) {
            $this->processFundsCredited($event);
        }
    }

    protected function updateTransactionStatus(TransactionStatusUpdated $event)
    {
        $transaction = $event->transaction;

        Http::post('https://external-service.com/api/update-status', [
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
        ]);
    }

    protected function processFundsCredited(FundsCredited $event)
    {
        $transaction = $event->transaction;

        Http::post('https://external-service.com/api/funds-credited', [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'status' => $transaction->status,
        ]);
    }
}

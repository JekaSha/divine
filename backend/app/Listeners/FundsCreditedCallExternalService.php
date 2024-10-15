<?php

namespace App\Listeners;

use App\Events\FundsCredited; // Include the FundsCredited event
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class FundsCreditedCallExternalService implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FundsCredited $event)
    {
print_r('FundsCredited');
        $this->processFundsCredited($event);

    }

    protected function processFundsCredited(FundsCredited $event)
    {
        $transaction = $event->transaction;

        Http::post("https://".env("OBMIN_HOST")."/api/funds-credited", [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'status' => $transaction->status,
        ]);
    }
}

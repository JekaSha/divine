<?php

namespace App\Listeners;

use App\Events\FundsCredited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Strategy; // Assuming this model exists

class HandleFundsCredited implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FundsCredited $event)
    {
        $transaction = $event->transaction;

        // Retrieve the strategy ID from the account associated with the wallet
        $strategy = $this->getStrategyIdForAccount($transaction->wallet->account_id);

        // Check if the strategy exists and is a valid class
        if ($strategy && class_exists($strategy->className)) {
            // Instantiate the strategy class
            $strategyInstance = new $strategy->className();

            // Execute the strategy with the transaction and event type
            $strategyInstance->execute($transaction);
        }
    }

    protected function getStrategyIdForAccount($accountId)
    {
        // Retrieve the strategy that corresponds to the event type
        return Strategy::where('account_id', $accountId)
            ->where('event_type', 'FundsCredited') // Specify the event type
            ->first(); // Adjust as needed to handle the result properly
    }
}

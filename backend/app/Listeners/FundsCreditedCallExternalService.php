<?php

namespace App\Listeners;

use App\Events\FundsCredited; // Include the FundsCredited event
use App\Services\ExchangeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class FundsCreditedCallExternalService implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FundsCredited $event, ExchangeService $exchangeService)
    {
        print_r('FundsCredited');
        $exchangeService->callExternalService($event->transaction, "completed");


    }


}

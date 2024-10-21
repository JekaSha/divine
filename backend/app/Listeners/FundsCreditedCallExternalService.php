<?php

namespace App\Listeners;

use App\Events\FundsCredited; // Include the FundsCredited event
use App\Services\ExchangeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class FundsCreditedCallExternalService implements ShouldQueue
{
    use InteractsWithQueue;
    protected $exchangeService;
    public function __construct(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function handle(FundsCredited $event)
    {

        $this->exchangeService->callExternalService($event->transaction, "completed");
    }


}

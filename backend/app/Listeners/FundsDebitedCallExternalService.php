<?php

namespace App\Listeners;

use App\Events\FundsDebited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\App;

class FundsDebitedCallExternalService implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FundsDebited $event,)
    {

        $exchangeService = App::make(ExchangeService::class);

        print_r('FundsDebitedCallExternalService');
        $exchangeService->callExternalService($event->transaction, "received");

    }


}

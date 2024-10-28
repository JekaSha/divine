<?php

namespace App\Listeners;

use App\Events\OrderCreated;

use App\Repositories\CurrencyRepository;
use App\Repositories\CurrencyProtocolRepository;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramNotifier;
use Illuminate\Support\Facades\Log;


class OrderCreatedTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue, TelegramNotifier;

    protected $currencyRepository;
    protected $protocolRepository;

    public function __construct(
        CurrencyRepository $currencyRepository,
        CurrencyProtocolRepository $protocolRepository,

    ) {

        $this->currencyRepository = $currencyRepository;
        $this->protocolRepository = $protocolRepository;

    }

    /**
     *
     * @param  \App\Events\OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {

        Log::debug('OrderCreated listener triggered.');

        $order = $event->order;
        $transaction = $order->transactions->first();
        $this->account = $transaction->wallet->account;

        $toCurrencyId = $order->currency_id;
        $toProtocolId = $order->protocol_id;

        $currencyId = $transaction->wallet->currency_id;
        $protocolId = $transaction->wallet->protocol_id;


        $fromCurrency = $this->currencyRepository->all(['id' =>$currencyId])->first();
        $fromProtocol = $this->protocolRepository->all(['id' =>$protocolId])->first();

        $toCurrency = $this->currencyRepository->all(['id' =>$toCurrencyId])->first();
        $toProtocol = $this->protocolRepository->all(['id' =>$toProtocolId])->first();

        $message = "Created Order:\nFrom: {$fromCurrency->name}-{$fromProtocol->name} To: {$toCurrency->name}-{$toProtocol->name}:\n\n{$order->wallet_address}\n\nAmount:{$order->amount}";

        $this->sendTelegramMessage($message);
    }
}

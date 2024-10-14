<?php

namespace App\Services\Strategies;

use App\Models\CurrencyProtocol;
use App\Services\Strategies\StrategyInterface;
use App\Models\Transaction;
use App\Models\Currency;
use App\Services\ExchangeApiService;
use App\Models\Order;

class QuickSellStrategy implements StrategyInterface
{
    protected $exchangeApiService;


    public function execute(Transaction $transaction)
    {

        $currencyId = $transaction->wallet->currency_id;
        $amount = $transaction->amount;
        $protocolId = $transaction->wallet->protocol_id;

        $this->exchangeApiService = new ExchangeApiService($transaction->wallet->account);

        $commission = 1;
        if ($com = $transaction->wallet->account->stream['commission_percent']) {
            $commission = 1 - $com/100;
        }

        $order = Order::where('transaction_id', $transaction->id)->first();
        $currencyName = Currency::find($currencyId)->name;
        $protocolName = CurrencyProtocol::find($protocolId)->name;

        $toCurrencyName = Currency::find($order->currency_id)->name;

        if ($this->exchangeApiService->checkPair($currencyName, $toCurrencyName)) {
            $receivedAmount = $amount * $commission;
            $response = $marketSellResponse = $this->exchangeApiService->market('sell', $currencyName, $toCurrencyName, $amount);
        } else {
            $r = $this->exchangeApiService->getOrderInfo(1893147633692327936, $toCurrencyName."-USDT");
            dd($r);
            //$response = $marketSellResponse = $this->exchangeApiService->market('sell', $currencyName, "USDT", $amount);
            $receivedAmount = $transaction->exchange_rate * $amount;
            $receivedAmount = $receivedAmount * $commission;
            $response = $marketBuyResponse = $this->exchangeApiService->market('buy', $toCurrencyName, "USDT", $receivedAmount);
            sleep(2);
            if ($response['status'] == 'success') {
                $r = $this->exchangeApiService->getOrderInfo($response['data']['order_id']);
                dd($r);
            }
        }

        if ($response['status'] === 'success') {


            //$receivedAmount = $marketSellResponse['data']['amount'];
            //$netAmount = $receivedAmount;
            $this->exchangeApiService->transferFunds($transaction->wallet_address, $receivedAmount, $currencyName, $protocolName);

        } else {
            // Handle errors, such as logging or updating transaction status
        }
    }
}

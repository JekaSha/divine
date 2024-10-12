<?php

namespace App\Services\Strategies;

use App\Models\CurrencyProtocol;
use App\Services\Strategies\StrategyInterface;
use App\Models\Transaction;
use App\Models\Currency;
use App\Services\ExchangeApiService;

class QuickSellStrategy implements StrategyInterface
{
    protected $exchangeApiService;


    public function execute(Transaction $transaction)
    {

        $currencyId = $transaction->wallet->currency_id;
        $amount = $transaction->amount;
        $protocolId = $transaction->wallet->protocol_id;

        $this->exchangeApiService = new ExchangeApiService($transaction->wallet->account);

        $currencyName = Currency::find($currencyId)->name;
        $protocolName = CurrencyProtocol::find($protocolId)->name;

        $marketSellResponse = $this->exchangeApiService->market('sell', $currencyName, $amount);

        if ($marketSellResponse['status'] === 'success') {

            $receivedAmount = $marketSellResponse['data']['amount'];
            $netAmount = $receivedAmount * 0.97;
            $this->exchangeApiService->transferFunds($transaction->wallet_address, $netAmount, $currencyName, $protocolName);

        } else {
            // Handle errors, such as logging or updating transaction status
        }
    }
}

<?php

namespace App\Services\Strategies;

use App\Models\CurrencyProtocol;
use App\Repositories\OrderRepository;
use App\Services\ExchangeService;
use App\Services\Strategies\StrategyInterface;
use App\Models\Transaction;
use App\Models\Currency;
use App\Services\ExchangeApiService;
use App\Models\Order;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;



class QuickSellStrategy implements StrategyInterface
{
    protected $exchangeApiService;

    protected $transactionRepository;
    protected $orderRepository;
    protected $walletRepository;
    protected $exchangeService;

    public function __construct(
        TransactionRepository $transactionRepository,
        WalletRepository $walletRepository,
        OrderRepository $orderRepository,
        ExchangeService $exchangeService,
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->walletRepository = $walletRepository;
        $this->orderRepository = $orderRepository;
        $this->exchangeService = $exchangeService;
    }
    public function execute(Transaction $transaction)
    {
        $debug = false;

        $currencyId = $transaction->wallet->currency_id;
        $amount = $transaction->amount;
        $protocolId = $transaction->wallet->protocol_id;

        $this->exchangeApiService = new ExchangeApiService($transaction->wallet->account);

        $commission = 1;
        if ($com = $transaction->wallet->account->stream['commission_convert_percent']) {
            $commission = 1 - $com/100;
        }

        $order = $this->orderRepository->get(['transaction_id' => $transaction->id])->first();

        $currencyName = Currency::find($currencyId)->name;
        $protocolName = CurrencyProtocol::find($protocolId)->name;

        $toCurrencyId = $order->currency_id;
        $toCurrencyName = Currency::find($toCurrencyId)->name;
        $toProtocolId = $order->protocol_id;

        if ($this->exchangeApiService->checkPair($currencyName, $toCurrencyName)) {
            $receivedAmount = $amount * $commission;
            $response = $marketSellResponse = $this->exchangeApiService->market('sell', $currencyName, $toCurrencyName, $amount);
        } else {

            if (!$debug) {
                sleep(5); //wait for deposited fully completed.
                $response = $marketSellResponse = $this->exchangeApiService->market('sell', $currencyName, "USDT", $amount);
                print_r($response);
                sleep(2);
            }
            $receivedAmount = $transaction->exchange_rate * $amount;
            $receivedAmount = $receivedAmount * $commission;
            if (!$debug) {
                $response = $marketBuyResponse = $this->exchangeApiService->market('buy', $toCurrencyName, "USDT", $receivedAmount);
                print_r($response);
            }
            $response['status'] = "success";
            sleep(2);
        }


        if ($response['status'] === 'success') {

            $account = $transaction->wallet->account;
            $r = $this->exchangeService->sendCurrencyToAddress(
                $account,
                $order->wallet_address,
                $receivedAmount,
                $toCurrencyId,
                $toProtocolId,
                $transaction
            );


        } else {
            // Handle errors, such as logging or updating transaction status
        }
    }
}

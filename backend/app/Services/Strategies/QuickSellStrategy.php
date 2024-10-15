<?php

namespace App\Services\Strategies;

use App\Models\CurrencyProtocol;
use App\Repositories\OrderRepository;
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

    public function __construct(
        TransactionRepository $transactionRepository,
        WalletRepository $walletRepository,
        OrderRepository $orderRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->walletRepository = $walletRepository;
        $this->orderRepository = $orderRepository;
    }
    public function execute(Transaction $transaction)
    {

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

        $toCurrencyName = Currency::find($order->currency_id)->name;

        if ($this->exchangeApiService->checkPair($currencyName, $toCurrencyName)) {
            $receivedAmount = $amount * $commission;
            $response = $marketSellResponse = $this->exchangeApiService->market('sell', $currencyName, $toCurrencyName, $amount);
        } else {

            //$response = $marketSellResponse = $this->exchangeApiService->market('sell', $currencyName, "USDT", $amount);
         //   print_r($response);
            sleep(2);
            $receivedAmount = $transaction->exchange_rate * $amount;
            $receivedAmount = $receivedAmount * $commission;
            //$response = $marketBuyResponse = $this->exchangeApiService->market('buy', $toCurrencyName, "USDT", $receivedAmount);
       //     print_r($response);
            $response['status'] = "success";
            sleep(2);
        }


        if ($response['status'] === 'success') {

            //$receivedAmount = $marketSellResponse['data']['amount'];
            //$netAmount = $receivedAmount;

            $toProtocolName = CurrencyProtocol::find($order->protocol_id)->name;
            print_r($receivedAmount);
            $rTransfer = $this->exchangeApiService->transferFunds($order->wallet_address, $receivedAmount, $toCurrencyName, $toProtocolName);
            print_r($rTransfer);

            if ($rTransfer['status'] == 'success') {

                $wallet = $this->walletRepository->create([
                        'wallet_token' => $order->wallet_address,
                        "currency_id" => $currencyId,
                        "protocol_id" => $protocolId,
                        "status" => "client",
                    ]
                );

                $rate = $rTransfer['data']['current_rate'] ?? 0;

                $transactionData = [
                    'wallet_id'      => $wallet->id,
                    'type'           => 'outgoing',
                    'status'         => 'created',
                    'amount'         => $rTransfer['data']['amount'],
                    'exchange_rate'  => $rate,
                    "order_id"       => $order->id,
                ];

                $transaction = $this->transactionRepository->create($transactionData);
                print_r($transaction);
            }
        } else {
            // Handle errors, such as logging or updating transaction status
        }
    }
}

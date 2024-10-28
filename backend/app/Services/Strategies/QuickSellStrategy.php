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

use Illuminate\Support\Facades\Log;



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


        $partner_commission = 1;
        if ($com = $transaction->wallet->account->stream['commission_convert_percent']) {
            $partner_commission = 1 - $com/100;
        }

        $order = $this->orderRepository->get(['transaction_id' => $transaction->id])->first();

        $currencyName = Currency::find($currencyId)->name;
        $protocolName = CurrencyProtocol::find($protocolId)->name;

        $toCurrencyId = $order->currency_id;
        $toCurrencyName = Currency::find($toCurrencyId)->name;
        $toProtocolId = $order->protocol_id;

        $exchangeId = $transaction->wallet->account->exchange_id;

        Log::info("currency:", ['currencyId' => $currencyId, "toCurrencyId" => $toCurrencyId]);
        $exchangeRate = $this->exchangeService->getExchangeRate($currencyId, $toCurrencyId, $exchangeId);

        $pair = "";
        $tmpCurrentName = $currencyName;
        $tmpToCurrenyName = $toCurrencyName;
        $pairExists = $this->exchangeApiService->checkPair($currencyName, $toCurrencyName, $pair);
        Log::info($pairExists, ['currencyName' => $currencyName, 'toCurrencyName' => $toCurrencyName]);
        if ($pairExists) {
            Log::info('Pair Exists:', ['from' => $currencyName, 'to' => $toCurrencyName]);

            $receivedAmount = $amount * $exchangeRate * $partner_commission;
            $side = "sell";
            if ($tmpCurrentName == $toCurrencyName) {
                $side = "buy";
                ///$amount = $receivedAmount;//$amount * $partner_commission;

                $currencyName = $toCurrencyName;
                $toCurrencyName = $tmpToCurrenyName;
            } else {

            }
            $response = $marketSellResponse = $this->exchangeApiService->market($side, $currencyName, $toCurrencyName, $amount);

            Log::info("Pair One change:",$response);
        } else {

            Log::Info('SELL MARKET: '. $currencyName);
            if (!$debug) {
                sleep(5); //wait for deposited fully completed.
                $response = $marketSellResponse = $this->exchangeApiService->market('sell', $currencyName, "USDT", $amount);
                Log::info($response);
                sleep(2);
            }

            $receivedAmount = $exchangeRate * $amount * $partner_commission;
            Log::Info('BUY MARKET: '. $toCurrencyName);
            if (!$debug) {
                $response = $marketBuyResponse = $this->exchangeApiService->market('buy', $toCurrencyName, "USDT", $receivedAmount);
                Log::info($response);
            }
            $response['status'] = "success";
            sleep(2);
        }

        if ($debug) {
            $response['status'] = "success";
        }

        if ($response['status'] === 'success') {

            Log::info("From: $currencyName = $amount" . "| To: $toCurrencyName = ".$receivedAmount);
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

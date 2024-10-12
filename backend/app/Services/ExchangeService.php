<?php

namespace App\Services;

use App\Events\FundsCredited;
use App\Events\TransactionStatusUpdated;
use App\Models\Account;
use App\Models\Currency;
use App\Models\CurrencyExchange;
use App\Models\CurrencyProtocol;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\CurrencyProtocolRepository;
use App\Repositories\CurrencyRepository;
use App\Repositories\WalletRepository;


class ExchangeService
{
    protected $currencyRepository;
    protected $protocolRepository;
    protected $walletRepository;
    protected $exchangeApiService;

    protected $userId = 1; //user exchanger

    public function __construct(
        CurrencyRepository $currencyRepository,
        CurrencyProtocolRepository $protocolRepository,
        WalletRepository $walletRepository
    )
    {
        $this->currencyRepository = $currencyRepository;
        $this->protocolRepository = $protocolRepository;
        $this->walletRepository = $walletRepository;
    }

    public function getData(array $currencyFilters = [], array $protocolFilters = [])
    {
        $currencies = $this->currencyRepository->all($currencyFilters);
        $protocols = $this->protocolRepository->all($protocolFilters);

        return [
            'currencies' => $currencies,
            'protocols' => $protocols,
        ];
    }

    public function getAvailableCurrencies()
    {
        return $this->walletRepository->getAvailableCurrencies($this->userId);
    }

    public function getExchangeRate($fromCurrencyId, $toCurrencyId, $exchangeId)
    {
        $fromCurrency = Currency::find($fromCurrencyId);
        $toCurrency = Currency::find($toCurrencyId);

        $this->exchangeApiService = new ExchangeApiService($exchangeId);

        $r = $this->exchangeApiService->getExchangeRate($fromCurrency->name, $toCurrency->name);

        if ($r['status'] == "success") {

            $rate = $this->extractRate($r);

            $currentRate = $rate ?? 0;

            CurrencyExchange::updateOrCreate(
                [
                    'from_currency_id' => $fromCurrencyId,
                    'to_currency_id' => $toCurrencyId,
                    'exchange_id' => $exchangeId,
                ],
                ['current_rate' => $currentRate]
            );

            return $currentRate;
        }

        return null;
    }

    public function postOrder($request) {

        $currencyId = $request->currency;
        $protocolId = $request->protocol;

        $wallet = Wallet::where('status', 'system')
            ->where('currency_id', $currencyId)
            ->where('user_id', $this->userId)
            ->first();

        $exchangeId = $wallet->account->exchange_id;
        $accountId = $wallet->account->id;

        $stream = $request->all();
        $exchangeRate = $this->getExchangeRate($request->currency, $request->target_currency, $exchangeId);

        $data = ['status' => 'error', "message" => "Cannot get exchange rate"];
        if ($exchangeRate) {
            $wallet = $this->getWallet($currencyId, $protocolId, $this->userId);

            if (!$wallet) {
                return ['status' => 'error', 'message' => 'Failed to create wallet.'];
            }

            // Create the transaction
            $transaction = Transaction::create([
                'wallet_id' => $wallet->id, // Use the newly created wallet ID
                'type' => 'incoming',
                'status' => 'pending',
                'amount' => $request->amount,
                'exchange_rate' => $exchangeRate,
                'expiry_time' => now()->addMinutes(30), // Example expiry time
            ]);

            // Create the order using the user-provided wallet address
            $order = Order::create([
                'transaction_id' => $transaction->id,
                'status' => 'pending',
                'user_id' => $this->userId, //guest
                'amount' => $request->amount,
                'wallet_address' => $request->wallet_address, // Use the user-provided wallet address
                'currency_id' => $request->target_currency,
                'protocol_id' => $request->target_protocol,
                'current_rate' => $exchangeRate,
                'stream' => $stream,
            ]);

            $data = [
                'status' => 'success',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'order_id' => $order->id,
                    'wallet_address' => $wallet->wallet_token, // The newly created wallet address
                    'received_amount' => $request->amount * $exchangeRate, // Calculate the received amount
                    'expiry_time' => $transaction->expiry_time->toDateTimeString(), // Format expiry time
                ],
            ];

        }

        return response()->json($data);

    }

    public function createWallet($accountId, $exchangeId, $currencyId, $protocolId) {

        $account = Account::find($accountId);
        $this->exchangeApiService = new ExchangeApiService($account);

        $currency = Currency::find($currencyId);
        $protocol = CurrencyProtocol::find($protocolId);

        $wallet = $this->getWallet($currency, $protocol);

        if ($wallet) {

            $wallet = Wallet::create([
                'account_id' => $accountId,
                'currency_id' => $currencyId,
                'protocol_id' => $protocolId,
                'wallet_token' => $wallet->address,
                'status' => 'active',
                'user_id' => $this->userId //guest
            ]);

        } else {
            return ['status' => 'error', 'message' => 'Failed to create wallet.'];
        }

        return $wallet;
    }

    public function getWallet($currencyId, $protokolId, $accountId) {

        $wallets = $this->walletRepository->get($currencyId, $protokolId, $accountId);

        if ($wallets->isNotEmpty()) {

            return $wallets->random();
        }

        return null;
    }

    public function extractRate($r):float {
        $rate = array_values($r['data']['symbols'])[0]['rate'];
        return $rate;
    }

    public function getExchangeTransactionHistory() {
        return $this->exchangeApiService->getTransactionHistory();
    }

    public function checkPendingTransactions()
    {

        $transactions = Transaction::whereNotIn('status', ['completed', 'failed', 'canceled'])->get();


        foreach ($transactions as $transaction) {
            $exchangeApiService = new ExchangeApiService($transaction->wallet->account);

            // Get transaction history from the exchange via exchangeApiService
            $history = $exchangeApiService->getTransactionHistory();

            if ($history['status'] === 'success') {
                foreach ($history['data'] as $exchangeTransaction) {
                    // Compare exchange data with our transaction
                    if ($this->isMatchingTransaction($exchangeTransaction, $transaction)) {
                        $newStatus = $this->determineNewStatus($exchangeTransaction);

                        // If the status has changed, update it and call the event
                        if ($transaction->status !== $newStatus) {
                            $transaction->status = $newStatus;
                            $transaction->save();

                            if ($newStatus === 'completed' && $transaction->amount <= $exchangeTransaction['amount']) {

                                $transaction->amount = $exchangeTransaction['amount'];
                                $order = Order::where('transaction_id', $transaction->id)->first();

                                $fromCurrencyName = Currency::find($transaction->wallet->currency_id)->name;
                                $toCurrencyName = Currency::find($order->currency_id)->name;
                                $exchangeId = $transaction->wallet->account->exchange_id;
                                $transaction->current_rate = $this->getExchangeRate($fromCurrencyName, $toCurrencyName, $exchangeId);
                                $transaction->save();

                                event(new FundsCredited($transaction)); // Trigger the funds credited event
                            } else {
                                event(new TransactionStatusUpdated($transaction));
                            }

                        }


                    }
                }
            }
        }
    }

    /**
     * Compares a transaction from the exchange with our local transaction.
     */
    protected function isMatchingTransaction($exchangeTransaction, $transaction)
    {
        return $exchangeTransaction['id'] === $transaction->id &&
            $exchangeTransaction['amount'] == $transaction->amount &&
            $exchangeTransaction['status'] !== $transaction->status;
    }

    /**
     * Determines new transaction status based on exchange data.
     */
    protected function determineNewStatus($exchangeTransaction)
    {
        switch ($exchangeTransaction['status']) {
            case 'completed':
                return 'completed'; // Transaction successful
            case 'failed':
                return 'failed'; // Transaction failed
            case 'pending':
            case 'in progress':
                return 'pending'; // Transaction is still being processed
            case 'refund':
                return 'canceled'; // Transaction was refunded
            default:
                return 'unknown'; // Any other statuses can be handled as needed
        }
    }



}

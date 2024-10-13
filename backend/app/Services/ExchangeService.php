<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;


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
use App\Repositories\TransactionRepository;
use App\Repositories\OrderRepository;


class ExchangeService
{
    protected $currencyRepository;
    protected $protocolRepository;
    protected $walletRepository;
    protected $exchangeApiService;
    protected $transactionRepository;
    protected $orderRepository;

    protected $userId = 1; //user exchanger

    public function __construct(
        CurrencyRepository $currencyRepository,
        CurrencyProtocolRepository $protocolRepository,
        WalletRepository $walletRepository,
        TransactionRepository $transactionRepository,
        OrderRepository $orderRepository
    )
    {
        $this->currencyRepository = $currencyRepository;
        $this->protocolRepository = $protocolRepository;
        $this->walletRepository = $walletRepository;
        $this->transactionRepository = $transactionRepository;
        $this->orderRepository = $orderRepository;
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
        $available = $this->walletRepository->getAvailableCurrencies($this->userId);

        return $available;
    }

    public function getExchangeRate($fromCurrencyId, $toCurrencyId, $exchangeId)
    {
        $fromCurrency = $this->currencyRepository->all(['id' =>$fromCurrencyId])->first();
        $toCurrency = $this->currencyRepository->all(['id' => $toCurrencyId])->first();

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

    use Illuminate\Support\Facades\DB;

    public function postOrder($validatedData) {
        $data = ['status' => 'error', "message" => "Cannot get exchange rate"];

        try {
            $currencyId        = $validatedData['currency'];
            $protocolId        = $validatedData['protocol'];
            $amount            = $validatedData['amount'];
            $targetCurrencyId  = $validatedData['target_currency'];
            $targetProtocolId  = $validatedData['target_protocol'];
            $walletAddress     = $validatedData['wallet_address'];
            $email             = $validatedData['email'];
            $stream            = $validatedData->all();

            // Fetch the wallet
            $wallet = $this->walletRepository->getFreeWallet($this->userId, $currencyId, $protocolId);

            if (!$wallet) {
                return ['status' => 'error', 'message' => 'Failed to create wallet.'];
            }

            $exchangeId = $wallet->account->exchange_id;

            $exchangeRate = $this->getExchangeRate($currencyId, $targetCurrencyId, $exchangeId);

            if (!$exchangeRate) {
                return response()->json($data);
            }

            // Wrap operations in a transaction
            $result = DB::transaction(function () use ($walletAddress, $amount, $email, $wallet, $exchangeRate, $stream, $targetCurrencyId, $targetProtocolId) {
                // Create the transaction (moved to repository)
                $transactionData = [
                    'wallet_id'      => $wallet->id,
                    'type'           => 'incoming',
                    'status'         => 'pending',
                    'amount'         => $amount,
                    'exchange_rate'  => $exchangeRate,
                    'expiry_time'    => now()->addMinutes(30),
                ];
                $transaction = $this->transactionRepository->create($transactionData);

                // Fetch target currency and protocol
                //$targetCurrency = $this->currencyRepository->get(['id' => $targetCurrencyId])->first();
                //$targetProtocol = $this->protocolRepository->get(['id' => $targetProtocolId])->first();

                if (!$targetCurrencyId || !$targetProtocolId) {
                    throw new \Exception('Invalid target currency or protocol.');
                }

                // Create the order (moved to repository)
                $orderData = [
                    'transaction_id' => $transaction->id,
                    'status'         => 'pending',
                    'user_id'        => $this->userId,
                    'amount'         => $amount,
                    'wallet_address' => $walletAddress,
                    'currency_id'    => $targetCurrencyId,
                    'protocol_id'    => $targetProtocolId,
                    'current_rate'   => $exchangeRate,
                    'email'          => $email,
                    'stream'         => $stream,
                ];
                $order = $this->orderRepository->create($orderData);

                return [
                    'status' => 'success',
                    'data'   => [
                        'transaction_id' => $transaction->id,
                        'order_id'       => $order->id,
                        'wallet_address' => $wallet->wallet_token,
                        'received_amount'=> $amount * $exchangeRate,
                        'expiry_time'    => $transaction->expiry_time->toDateTimeString(),
                    ],
                ];
            });

            return response()->json($result);

        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            // Optionally log the exception: Log::error($e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
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

    public function getWallets($currencyId, $protokolId, $userId) {

        $wallets = $this->walletRepository->get($currencyId, $protokolId, $userId);

        return $wallets;
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

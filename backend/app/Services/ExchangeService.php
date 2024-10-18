<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Events\FundsCredited;
use App\Events\FundsDebited;
use App\Events\IncomingTransactionStatusUpdated;
use App\Events\OutgoingTransactionStatusUpdated;
use App\Events\OutgoingTransactionCreated;


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

    public function postOrder($validatedData) {
        $data = ['status' => 'error', "code" => "109", "msg" => "Cannot get exchange rate"];

        try {
            $currencyId        = $validatedData['currency'];
            $protocolId        = $validatedData['protocol'];
            $amount            = $validatedData['amount'];
            $targetCurrencyId  = $validatedData['target_currency'];
            $targetProtocolId  = $validatedData['target_protocol'];
            $walletAddress     = $validatedData['wallet_address'];
            $email             = $validatedData['email'];
            $stream            = $validatedData;

            // Fetch the wallet
            $wallet = $this->walletRepository->getFreeWallet($this->userId, $currencyId, $protocolId);

            if (!$wallet) {
                return ['status' => 'error', 'code' => 112,'msg' => 'Failed to create wallet.'];
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
                    'status'         => 'created',
                    'amount'         => $amount,
                    'exchange_rate'  => $exchangeRate,
                    'expiry_time'    => now()->addMinutes(30),
                ];
                $transaction = $this->transactionRepository->create($transactionData);

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
                        'hash'           => $order->hash,
                    ],
                ];
            });

            return $result;

        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            // Optionally log the exception: Log::error($e);
            $orderId = $this->extractOrderIdFromMessage($e->getMessage());

            if ($orderId) {
                $existingOrder = $this->orderRepository->get(['id' => $orderId, "transaction_type" => "incoming"])->first();

                if ($existingOrder) {
                    return response()->json([
                        'status' => 'error',
                        "code" => 105,
                        'msg' => 'An identical order already exists.',
                        'data' => [
                            'order_id' => $existingOrder->id,
                            'wallet_address' => $existingOrder->transactions->first()->wallet->wallet_token,
                            'received_amount' => $existingOrder->amount * $existingOrder->current_rate,
                            'expiry_time' => $existingOrder->transactions->first()->expiry_time,
                            'hash' => $existingOrder->hash,
                        ],
                    ]);
                }
            }
        }
    }

    private function extractOrderIdFromMessage($message)
    {

        if (preg_match('/Order ID: (\d+)/', $message, $matches)) {
            return (int) $matches[1];
        }
        return null;
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
            return ['status' => 'error', "code" => 108,'msg' => 'Failed to create wallet.'];
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

    public function IncomingTransactionsCheck()
    {
/*
        $id = 98;
        $trans = Transaction::find($id);
        $r = $this->callExternalService($trans, 'received');
        dd($r);

        $id = 0;
        if ($id) {
            $trans = Transaction::find($id);
            $trans->status = "created";
            $trans->save();
        }
*/
        $transactions = $this->transactionRepository->get(
            [
                '!status' => ['completed', 'failed', 'canceled'],
                "!expired" => true
            ]
        );
//dd($transactions);
        $histories = [];
        foreach ($transactions as $transaction) {
            $account = $transaction->wallet->account;
            $exchangeApiService = new ExchangeApiService($account);

            $accountId = $account->id;

            // Get transaction history from the exchange via exchangeApiService
            if (!isset($histories[$accountId])) {

                $history = $exchangeApiService->getTransactionHistory();
                $histories[$accountId] = $history;
                print_R($histories);
            } else {
                $history = $histories[$accountId];
            }

            if ($history['status'] === 'success') {
                foreach ($history['data'] as $exchangeTransaction) {
                    // Compare exchange data with our transaction

                    if ($this->isMatchingTransaction($exchangeTransaction, $transaction)) {
                        $newStatus = $exchangeTransaction['status'];

                        // If the status has changed, update it and call the event
                        if ($transaction->status !== $newStatus) {
                            print_R($transaction->status . " = " .$newStatus);
                            $transaction->status = $newStatus;
                            $transaction->save();

                            if ($transaction->type == 'incoming') {

                                $order = Order::find($transaction->order->id);
                                $order->status = "received";
                                $order->save();

                                event(new IncomingTransactionStatusUpdated($transaction));

                                if ($newStatus === 'completed'
                                    && (float)$transaction->amount <= (float)$exchangeTransaction['amount']
                                ) {

                                    $transaction->amount = $exchangeTransaction['amount'];
                                    $order = $this->orderRepository->get(['transaction_id' => $transaction->id])->first(); //return Order witch has this transaction_id

                                    $fromCurrencyId = $transaction->wallet->currency_id;
                                    $toCurrencyId = $order->currency_id;
                                    $exchangeId = $transaction->wallet->account->exchange_id;

                                    $transaction->exchange_rate = $this->getExchangeRate($fromCurrencyId, $toCurrencyId, $exchangeId);

                                    $transaction->save();

                                    event(new FundsCredited($transaction)); // Trigger the funds credited event
                                }
                            }
                        }


                    }
                }
            }
        }
    }

    public function OutgoingTransactionsCheck()
    {

        $transactions = $this->transactionRepository->get(
            [
                '!status' => ['completed', 'failed', 'canceled'],
                'type' => 'outgoing'
            ]
        );


        $histories = [];

        foreach ($transactions as $transaction) {
            $account = $transaction->wallet->account;
            $exchangeApiService = new ExchangeApiService($account);

            $accountId = $account->id;

            $currency = $this->currencyRepository->all(['id' => $transaction->wallet->currency_id])->first()->name;

            // Get transaction history from the exchange via exchangeApiService
            if (!isset($histories[$accountId][$currency])) {

                $history = $exchangeApiService->getOutgoingTransactionsHistory($currency);
                $histories[$accountId][$currency] = $history;


            } else {
                $history = $histories[$accountId][$currency];
            }

            foreach ($history as $exchangeTransaction) {

                if ($this->isMatchingOutgoingTransaction($currency, $exchangeTransaction, $transaction)) {

                    if ($exchangeTransaction['status'] !== $transaction->status) {
                        $transaction->status = $exchangeTransaction['status'];
                        $transaction->save();


                        event(new OutgoingTransactionStatusUpdated($transaction));

                        if ($transaction->status == 'completed') {

                            $order = $transaction->order->first();
                            $order->status = "completed";
                            $order->save();

                            event(new FundsDebited($transaction));
                        }
                    }
                    break;
                }
            }

        }

    }

    protected function isMatchingOutgoingTransaction($currency, $exchangeTransaction, $transaction) {
        return $exchangeTransaction['amount'] == $transaction->amount &&
            $exchangeTransaction['wallet'] == $transaction->wallet->wallet_token &&
            $exchangeTransaction['currency'] == $currency &&
            $exchangeTransaction['time'] > strtotime($transaction->created_at);
    }


    /**
     * Compares a transaction from the exchange with our local transaction.
     */
    protected function isMatchingTransaction($exchangeTransaction, $transaction)
    {

        return $exchangeTransaction['wallet'] === $transaction->wallet->wallet_token &&
            $exchangeTransaction['amount'] == $transaction->amount &&
            $exchangeTransaction['status'] !== $transaction->status &&
            $exchangeTransaction['time'] > strtotime($transaction->created_at);
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

    public function sendCurrencyToAddress(Account $account, $address, $amount, $currencyId, $protocolId, Transaction $transaction = null) {

        $this->exchangeApiService = new ExchangeApiService($account);

        $currencyName = $this->currencyRepository->all(['id' => [$currencyId]])->first()->name;
        $protocolName = $this->protocolRepository->all(['id' => [$protocolId]])->first()->name;

        if (isset($transaction->id)) {
            $order = $this->orderRepository->get(['transaction_id' => $transaction->id])->first();
        }

        $rTransfer = $this->exchangeApiService->transferFunds($address, $amount, $currencyName, $protocolName);
/*
        $rTransfer['status'] = 'success';
        $rTransfer['data'] = [];
        $rTransfer['data']['amount'] = 100;
*/
        if ($rTransfer['status'] == 'success') {

            DB::beginTransaction();

            try {

                $walletData = [
                    'account_id' => $account->id,
                    'wallet_token' => $address,
                    "currency_id" => $currencyId,
                    "protocol_id" => $protocolId,
                    "status" => "client",
                ];

                $wallet = $this->walletRepository->create($walletData);


                $rate = $rTransfer['data']['current_rate'] ?? 0;

                $transactionData = [
                    'wallet_id' => $wallet->id,
                    'type' => 'outgoing',
                    'status' => 'created',
                    'amount' => $rTransfer['data']['amount'],
                    'exchange_rate' => $rate,
                ];

                if (isset($order->id)) {
                    $transactionData['order_id'] = $order->id;
                }
                //print_r($transactionData);
                $transaction = $this->transactionRepository->create($transactionData);
                //print_r($transaction);

                DB::commit();

                $r = ['status' => "success", "data" =>
                    [
                        'transfer' => $rTransfer,
                        'transaction' => $transaction,
                        'wallet' => $wallet
                    ]
                ];

                event(new OutgoingTransactionCreated($transaction));

                return $r;

            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Error in sendCurrencyToAddress: ' . $e->getMessage());
                throw $e;
            }
        }

        return ['status' => 'error', 'msg' => 'Some error in sendCurrencyToAddress'];
    }

    public function callExternalService(Transaction $transaction, $status) {

        $stream = $transaction->wallet->account->stream;

        if (isset($stream['service_external']) &&
            ($stream['service_external']['host'] && $stream['service_external']['api_key'])) {
            $serviceExternal =
                [
                    'host' => $stream['service_external']['host'],
                    'api' => $stream['service_external']['api_key'],
                ];
            Log::info("Send request to {$serviceExternal['host']}");

            if ($transaction->order->first()->stream['external_order_id']) {

                try {
                    $r = Http::withHeaders(['api_key' => $serviceExternal['api']])->post("https://" . $serviceExternal['host'] . "/api/funds-debited", [
                        'transaction_id' => $transaction->id,
                        'order_id' => $transaction->order->first()->stream['external_order_id'],
                        'status' => $status,
                    ]);
                    return $r;
                }  catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            } else {
                Log::error("Error external order id in: {$transaction->order->first()->id}");
            }
        }


    }



}

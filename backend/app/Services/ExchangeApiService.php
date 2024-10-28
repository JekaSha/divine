<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Exchange;
use App\Repositories\Exchanges\ExchangeApiFactory;

class ExchangeApiService {

    protected $api;

    public function __construct($account) {

        if ($account instanceof Account) {
            if ($account->exchange) {
                $exchangeName = $account->exchange->name;

                $key = trim($account->api_key);
                $secret = trim($account->api_secret);
                $stream = $account->stream;

                $this->api = ExchangeApiFactory::create($exchangeName, $key, $secret, $stream);
            } else {
                throw new \InvalidArgumentException("Account must have an associated exchange.");
            }
        } else {
            $exchange = Exchange::find($account);
            if ($exchange) {
                $this->api = ExchangeApiFactory::create($exchange->name);
            } else {
                throw new \InvalidArgumentException("Exchange not found for the given account ID.");
            }
        }

    }

    public function getExchangeRate(string $from, string $to) {

        $rate = $this->api->getExchangeRate($from, $to);

        $response = ["status" => "error"];
        if ($rate) {
            $response = ['status' => "success", "data" =>
                ['symbols' => [
                    "{$from}{$to}" => [
                        "symbol" => "{$from}{$to}",
                        "rate" => $rate
                    ]
                    ]
                ]
            ];
        }

        return $response;
    }

    public function createWallet($currency, $protocol) {

        $wallet = $this->api->createWallet($currency, $protocol);
        dd($wallet);
        return $wallet;
    }


    public function getTransactionHistory(string $symbol = null)
    {

        $history = $this->api->getTransactionHistory($symbol);

        if ($history) {
            return [
                'status' => 'success',
                'data' => $history
            ];
        }

        return ['status' => 'error', 'message' => 'Unable to fetch transaction history'];
    }


    public function market($type, $fromCurrency, $toCurrency, $amount) {

        $r = $this->api->market($type, $fromCurrency,$toCurrency, $amount);
        return $r;
    }
    public function transferFunds($walletAddress, $amount, $currency, $protocol) {
        $r = $this->api->transferFunds($walletAddress, $amount, $currency, $protocol);
        return $r;
    }

    public function checkPair(&$fromCurrency, &$toCurrency, &$pair) {
        $r = $this->api->checkPair($fromCurrency, $toCurrency, $pair);
        return $r;
    }

    public function getOrderInfo($orderId, $pair) {

        $r = $this->api->getOrderInfo($orderId, $pair);
        return $r;

    }

    public function transfer($currency, $amount) {

        $r = $this->api->transfer($currency, $amount);
        return $r;

    }

    public function getOutgoingTransactionsHistory($currency, $protocol = null) {

        $history = $this->api->getOutgoingTransactionsHistory($currency, $protocol = null);

        return $history;
        if ($history) {
            return [
                'status' => 'success',
                'data' => $history,
            ];
        }

        return ['status' => 'error', 'message' => 'Unable to fetch transaction history'];
    }

}

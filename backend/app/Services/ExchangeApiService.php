<?php

namespace App\Services;

use App\Repositories\Exchanges\ExchangeApiFactory;
use App\Models\Account;
use App\Models\Exchange;

class ExchangeApiService {

    protected $api;

    public function __construct($account) {

        if ($account instanceof Account) {
            if ($account->exchange) {
                $exchangeName = $account->exchange->name;
                $key = $account->api_key;
                $secret = $account->api_secret;

                $this->api = ExchangeApiFactory::create($exchangeName, $key, $secret);
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

        return $rate;
    }

}

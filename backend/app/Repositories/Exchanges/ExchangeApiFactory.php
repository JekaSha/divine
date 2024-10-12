<?php

namespace App\Repositories\Exchanges;

use App\Repositories\Exchanges\OkxApi;
use App\Repositories\Exchanges\BitfinexApi;

class ExchangeApiFactory
{
    public static function create(string $exchangeName, string $api_key = "", string $api_secret = "", $stream = []): ExchangeApiInterface
    {
        switch (strtolower($exchangeName)) {
            case 'okx':
                return new OkxApi($api_key, $api_secret, $stream);
            case 'bitfinex':
                return new BitfinexApi($api_key, $api_secret, $stream);
            default:
                throw new \Exception("Exchange API not found.");
        }
    }
}

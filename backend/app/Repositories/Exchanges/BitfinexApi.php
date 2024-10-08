<?php

namespace App\Repositories\Exchanges;

use App\Repositories\Exchanges\ExchangeApiInterface;
use Illuminate\Support\Facades\Http;

class BitfinexApi implements ExchangeApiInterface
{
    protected $baseUrl = 'https://api.bitfinex.com/v2';

    protected $key;
    protected $secret;

    public function __construct($key = "", $secret = "") {

        $this->key = $key;
        $this->secret = $secret;
    }

    public function getExchangeRate($fromCurrency, $toCurrency)
    {
        $symbol = "{$fromCurrency}{$toCurrency}";
        $symbol = str_replace("USDT", "UST", $symbol);

        $url = "{$this->baseUrl}/ticker/t$symbol";
        $response = Http::get
        (
            $url
        );


        if ($response->successful()) {
            $json = $response->json();
            return $json[6];
        }

        return null;
    }
}

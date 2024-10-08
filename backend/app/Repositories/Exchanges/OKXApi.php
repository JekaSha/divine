<?php

namespace App\Repositories\Exchanges;

use App\Repositories\Exchanges\ExchangeApiInterface;
use Illuminate\Support\Facades\Http;

class OkxApi implements ExchangeApiInterface
{
    protected $baseUrl = 'https://www.okx.com/api/v5';
    protected $key;
    protected $secret;

    public function __construct($key = "", $secret = "") {

        $this->key = $key;
        $this->secret = $secret;
    }

    public function getExchangeRate($fromCurrency, $toCurrency)
    {
        $response = Http::get("{$this->baseUrl}/market/exchange-rate", [
            'instId' => "{$fromCurrency}-{$toCurrency}"
        ]);

        if ($response->successful()) {
            return $response->json()['data'] ?? null;
        }

        return null;
    }
}

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

            if (isset($json[6])) {
                return $json[6];
            } else {


            }

        } else {
            $rate = $this->getExchangeRate($toCurrency, $fromCurrency);
            $rate = 1/$rate;
            return $rate;
        }

        return null;
    }

    public function createWallet($currency, $protocol)
    {

        $url = $this->baseUrl."/auth/w/deposit/address";
        $nonce = (string) (time() * 1000 * 1000);

        $data = [
            'wallet' => 'margin',
            'method' => 'tetheruso',
            'op_renew' => 1,

        ];

        $body = $url . $nonce . json_encode($data, JSON_UNESCAPED_SLASHES);

        $signature = hash_hmac('sha384', $body, "5d77c7d579305413807f1a1001a40aee9428b591fb8");

        $headers = [
            'bfx-nonce: ' . $nonce,
            'bfx-apikey: ' . "b8e2ad028e90ed3414775c483b415449aa1dcf2eae0",
            'bfx-signature: ' . $signature,
            'Content-Type: application/json',
            'Accept' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->post($url, $data);
dd($response->body());
        if ($response->successful()) {
            return $response->json();
        } else {
            return $response->body();
        }
    }
}

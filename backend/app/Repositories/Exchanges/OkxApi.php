<?php

namespace App\Repositories\Exchanges;

use App\Repositories\Exchanges\ExchangeApiInterface;
use Illuminate\Support\Facades\Http;

class OkxApi implements ExchangeApiInterface
{
    protected $baseUrl = 'https://www.okx.com/api/v5';
    protected $key;
    protected $stream;
    protected $secret;

    public function __construct($key = "", $secret = "", $stream) {

        $this->key = $key;
        $this->secret = $secret;
        $this->stream = $stream;
    }

    public function getExchangeRate($fromCurrency, $toCurrency)
    {
        $data = [
            'instId' => $this->normalizePair($fromCurrency, $toCurrency)
        ];

        $url = "{$this->baseUrl}/market/ticker";

        $r = $this->sendRequest('GET', $url, $data);

        if ($r['code'] == 51001) {

            // Try the inverse pair
            $data = [
                'instId' => $this->normalizePair($toCurrency, $fromCurrency)
            ];

            $r = $this->sendRequest('GET', $url, $data);
            if ($r['code'] == '0') {
                return 1 / (float)($r['data'][0]['last']);
            }

            // If inverse pair also fails, calculate via USDT

            // Get fromCurrency to USDT rate
            $dataFromUSDT = [
                'instId' => $this->normalizePair($fromCurrency, 'USDT')
            ];

            $rFromUSDT = $this->sendRequest('GET', $url, $dataFromUSDT);

            if ($rFromUSDT['code'] != '0') {
                // Cannot get fromCurrency to USDT rate
                return null;
            }

            $fromUSDT = (float)$rFromUSDT['data'][0]['last'];

            // Get toCurrency to USDT rate
            $dataToUSDT = [
                'instId' => $this->normalizePair($toCurrency, 'USDT')
            ];

            $rToUSDT = $this->sendRequest('GET', $url, $dataToUSDT);

            if ($rToUSDT['code'] != '0') {
                // Cannot get toCurrency to USDT rate
                return null;
            }

            $toUSDT = (float)$rToUSDT['data'][0]['last'];

            // Now compute the rate between fromCurrency and toCurrency
            $exchangeRate = $fromUSDT / $toUSDT;
            return $exchangeRate;
        }

        if ($r['code'] == '0') {
            return (float)$r['data'][0]['last'];
        }

        return null;
    }



    public function createWallet($currency, $protocol)
    {
        // TODO: Implement createWallet() method.
    }

    public function getTransactionHistory()
    {

        $url = "{$this->baseUrl}/trade/fills-history";

        $params = [
            'instType' => 'SPOT',
        ];

        return $this->sendRequest('GET', $url, $params);
    }



    public function market($type, $from, $to, $amount)
    {
        $url = "{$this->baseUrl}/trade/order";

        $pair = $this->normalizePair($from, $to);

        $params = [
            'instId' => $pair,
            'tdMode' => 'cash',
            'side' => $type,
            'ordType' => 'market',
            'sz' => $amount,
        ];

        return $this->sendRequest('POST', $url, $params);
    }

    public function normalizePair($from, $to) {

        $pair = $from."-".$to;
        return $pair;
    }

    public function transferFunds($toAddress, $amount, $currency, $protocol)
    {
        $url = "{$this->baseUrl}/asset/transfer";

        $params = [
            'ccy' => $currency,
            'amt' => $amount,
            'dest' => '4',
            'toAddr' => $toAddress,
            'protocol' => $protocol
        ];

        return $this->sendRequest('POST', $url, $params);
    }


    protected function sendRequest($method, $url, $params = [])
    {
        $timestamp = now()->format('Y-m-d\TH:i:s.v\Z');
        if (strtoupper($method) === 'GET' && !empty($params)) {
            $queryString = http_build_query($params); // Convert params to query string
            $requestPath = parse_url($url, PHP_URL_PATH) . '?' . $queryString; // Append query string
        } else {
            $requestPath = parse_url($url, PHP_URL_PATH); // Extract the path from the URL
        }

        $body = in_array($method, ['POST', 'PUT']) ? json_encode($params) : ''; // Only encode params for POST/PUT requests

        $headers = [
            'Content-Type' => 'application/json',
        ];


        // Add authentication headers if the API key exists
        if (!empty($this->key)) {

            $signature = $this->generateSignature($timestamp, strtoupper($method), $requestPath, $body);

            $headers['OK-ACCESS-KEY'] = trim($this->key);
            $headers['OK-ACCESS-SIGN'] = $signature;
            $headers['OK-ACCESS-TIMESTAMP'] = $timestamp;
            $headers['OK-ACCESS-PASSPHRASE'] = $this->stream['passphrase'];
        }


        // Make the request
        $response = Http::withHeaders($headers)->$method($url, $params);

        // Check the response and return the result
        if ($response->successful()) {
            return $response->json();
        }

        return [
            'status' => 'error',
            'message' => $response->json()['msg'] ?? 'Request failed.',
        ];
    }


    protected function generateSignature($timestamp, $method = 'GET', $requestPath = '/api/v5/trade/fills-history', $body = '')
    {
        $stringToSign = $timestamp . strtoupper($method) . $requestPath . $body;
        return base64_encode(hash_hmac('sha256', $stringToSign, trim($this->secret), true));
    }


}

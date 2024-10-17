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

    public function getTransactionHistory($currency = null, $limit = 100)
    {

        $url = "{$this->baseUrl}/asset/deposit-history";

        $params = [
            'ccy' => $currency,
            'limit' => $limit,
        ];

        $response = $this->sendRequest('GET', $url, $params);

        if ($response['code'] == '0') {
            foreach ($response['data'] as &$transaction) {
                $data = $transaction;
                $protocol = str_replace($transaction['ccy']."-", "", $transaction['chain']);
                $status = $this->determineNewStatus($transaction);

                $transaction = [
                    'currency' => strtoupper($transaction['ccy']),
                    'protocol' => strtoupper($protocol),
                    'amount' => $transaction['amt'],
                    "wallet" => $transaction['to'],
                    "txId" => $transaction['txId'],
                    "time" => $transaction['ts']/1000,
                    "status" => $status,
                    'source' => $data,
                ];


            }
        }

        return $response['data'];
    }

    public function getOutgoingTransactionsHistory($currency, $protocol = null, $limit = 100) {

        $url = "{$this->baseUrl}/asset/withdrawal-history";
        $params = [
            'ccy' => $currency,
            'limit' => $limit,
        ];

        $response = $this->sendRequest('GET', $url, $params);

        if ($response['code'] == '0') {
            if (count($response['data']) > 0) {

                foreach ($response['data'] as &$transaction) {
                    $data = $transaction;
                    $protocol = str_replace($transaction['ccy'] . "-", "", $transaction['chain']);
                    $status = $this->determineNewStatus($transaction);

                    $transaction = [
                        'currency' => strtoupper($transaction['ccy']),
                        'protocol' => strtoupper($protocol),
                        'amount' => $transaction['amt'],
                        "wallet" => $transaction['to'],
                        "txId" => $transaction['txId'],
                        "time" => $transaction['ts'] / 1000,
                        "status" => $status,
                        'source' => $data,
                    ];

                    $r[] = $transaction;
                }
            } else {

                return [];

            }
        }

        return $r;
    }

    public function getWithdrawalHistory($currency = null, $limit = 100)
    {
        $url = "{$this->baseUrl}/asset/withdrawal-history";

        $params = [
            'ccy' => $currency,
            'limit' => $limit,
        ];

        $response = $this->sendRequest('GET', $url, $params);

        if ($response['code'] == '0') {
            foreach ($response['data'] as &$transaction) {
                $data = $transaction;
                $protocol = str_replace($transaction['ccy'] . "-", "", $transaction['chain']);
                $status = $this->determineWithdrawalStatus($transaction);

                $transaction = [
                    'currency' => strtoupper($transaction['ccy']),
                    'protocol' => strtoupper($protocol),
                    'amount' => $transaction['amt'],
                    "wallet" => $transaction['toAddr'],
                    "txId" => $transaction['txId'],
                    "time" => $transaction['ts'],
                    "status" => $status,
                    'fee' => $transaction['fee'],
                    'source' => $data,

                ];
            }
        }

        return $response['data'];
    }

    protected function determineWithdrawalStatus($transaction)
    {
        print("DT:".$transaction['state']);
        switch ($transaction['state']) {
            case '0':
                return 'pending';      // Вывод в процессе
            case '1':
                return 'pending';      // В процессе обработки
            case '2':
                return 'failed';       // Неудачный вывод
            case '3':
                return 'pending';      // На проверке
            case '4':
                return 'pending';      // Проверка завершена
            case '5':
                return 'pending';      // Отправлено в блокчейн
            case '6':
                return 'completed';    // Успешный вывод
            case '7':
                return 'canceled';     // Отменено пользователем
            case '8':
                return 'rejected';     // Отклонено системой
            default:
                return 'unknown';      // Неизвестный статус
        }
    }


    protected function determineNewStatus($exchangeTransaction) {

        switch ($exchangeTransaction['state']) {
            case '0':
                return "detected";
            case '2':
                return 'completed';
            case '3':
                return 'failed';
            case '1':
            case '5':
                return 'pending'; // Transaction is still being processed
            case '4':
                return 'canceled'; // Transaction was refunded
            default:
                return 'unknown'; // Any other statuses can be handled as needed
        }
    }


    public function market($side, $from, $to, $amount)
    {
        $url = "{$this->baseUrl}/trade/order";

        $pair = $this->normalizePair($from, $to);

        if ($side == 'buy')  {
            $rate = $this->getExchangeRate($from, $to);
            $amount = $amount * $rate;
        }

        $type = "market";
        $params = [
            'instId' => $pair,
            'tdMode' => 'cash',
            'side' => $side,
            'ordType' => $type,
            'sz' => $amount,
        ];
/*
 * "code" => "0"
  "data" => array:1 [
    0 => array:6 [
      "clOrdId" => ""
      "ordId" => "1893147633692327936"
      "sCode" => "0"
      "sMsg" => "Order placed"
      "tag" => ""
      "ts" => "1728922596106"
    ]
  ]
  "inTime" => "1728922596106312"
  "msg" => ""
  "outTime" => "1728922596108227"

 */
        $response = ['status' => 'error'];
        $r =  $this->sendRequest('POST', $url, $params);
        if ($r['code'] == 0) {
            //sleep(2);
            //$o = $this->getOrderInfo($r['data'][0]['ordId'], $from, $to);

            $response = ['status' => 'success',
                "data" => [
                    "order_id" => $r['data'][0]['ordId'],
                    "symbol" => $pair,
                    "side" => $side,
                    "type" => $type
                ]
            ];
        }

        return $response;
    }

    public function normalizePair($from, $to) {

        $pair = $from."-".$to;
        return $pair;
    }

    public function transferFunds($toAddress, $amount, $currency, $protocol)
    {
        $url = "{$this->baseUrl}/asset/withdrawal";

        $fee = $this->getWithdrawalFee($currency, $protocol);

        $this->transfer($currency, $amount+$fee);

        $params = [
            'ccy' => $currency,
            'amt' => (string)$amount,
            'dest' => '4',
            'toAddr' => trim($toAddress),
           // 'chain' => strtoupper($currency."-".$protocol),
            "chainName" => $protocol,
            "fee" => $fee,

        ];

print_R($params);
        $r = $this->sendRequest('POST', $url, $params);

        $code = 801;
        if ($r['code'] == 0) {
            $data = [
                'status'  => 'success',
                "data" => [
                    'amount' => $amount,
                    'currency' => $currency,
                    'protocol' => $protocol,
                    "sentToAddress" => $toAddress,
                ]
            ];
            return $data;
        } elseif ($r['code'] == 58206) {
            $code = 802; //low amount in wallet
        } elseif ($r['code'] == 58207) {
            $code = 803; //can't do transfer funds to this address
        } else {
            print_r($r);
        }

        $r = ['status' => "error", "msg" => $r['msg'], "code" => $code];
        return $r;
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

    public function checkPair($fromCurrency, $toCurrency)
    {
        $pair = $this->normalizePair($fromCurrency, $toCurrency);
        $url = "{$this->baseUrl}/market/products";

        $response = $this->sendRequest('GET', $url);

        if (isset($response['code']) && $response['code'] === '0') {
            foreach ($response['data'] as $product) {
                if ($product['instId'] === $pair) {
                    return true;
                }
            }
        }

        return false;
    }


    /*
     * array:3 [
  "code" => "0"
  "data" => array:1 [
    0 => array:51 [
      "accFillSz" => "1.250229"
      "algoClOrdId" => ""
      "algoId" => ""
      "attachAlgoClOrdId" => ""
      "attachAlgoOrds" => []
      "avgPx" => "4.348"
      "cTime" => "1728922596106"
      "cancelSource" => ""
      "cancelSourceReason" => ""
      "category" => "normal"
      "ccy" => ""
      "clOrdId" => ""
      "fee" => "-0.001250229"
      "feeCcy" => "DOT"
      "fillPx" => "4.348"
      "fillSz" => "1.250229"
      "fillTime" => "1728922596107"
      "instId" => "DOT-USDT"
      "instType" => "SPOT"
      "isTpLimit" => "false"
      "lever" => ""
      "linkedAlgoOrd" => array:1 [
        "algoId" => ""
      ]
      "ordId" => "1893147633692327936"
      "ordType" => "market"
      "pnl" => "0"
      "posSide" => "net"
      "px" => ""
      "pxType" => ""
      "pxUsd" => ""
      "pxVol" => ""
      "quickMgnType" => ""
      "rebate" => "0"
      "rebateCcy" => "USDT"
      "reduceOnly" => "false"
      "side" => "buy"
      "slOrdPx" => ""
      "slTriggerPx" => ""
      "slTriggerPxType" => ""
      "source" => ""
      "state" => "filled"
      "stpId" => ""
      "stpMode" => "cancel_maker"
      "sz" => "5.436"
      "tag" => ""
      "tdMode" => "cash"
      "tgtCcy" => "quote_ccy"
      "tpOrdPx" => ""
      "tpTriggerPx" => ""
      "tpTriggerPxType" => ""
      "tradeId" => "98439316"
      "uTime" => "1728922596111"
    ]
  ]
  "msg" => ""
] // app/Repositories/Exchanges/OkxApi.php:300
jekas@MacBook-A
     */
    public function getOrderInfo($orderId, $from, $to)
    {
        $url = "{$this->baseUrl}/trade/order";

        $params = [
            'ordId' => $orderId,
            'instId' => $from."-".$to,
        ];


        $response = $this->sendRequest('GET', $url, $params);


        if (isset($response['code']) && $response['code'] == '0') {
            return $response['data'][0];
        }

        return [
            'status' => 'error',
            'message' => $response['msg'] ?? 'Failed to retrieve order information.',
        ];
    }

    /*
     * "data" => array:375 [
    0 => array:25 [
      "burningFeeRate" => ""
      "canDep" => true
      "canInternal" => true
      "canWd" => true
      "ccy" => "USDT"
      "chain" => "USDT-TRC20"
      "depQuotaFixed" => ""
      "depQuoteDailyLayer2" => ""
      "logoLink" => "https://static.coinall.ltd/cdn/oksupport/asset/currency/icon/usdt20240813135750.png"
      "mainNet" => false
      "maxFee" => "2"
      "maxFeeForCtAddr" => "2"
      "maxWd" => "32699700"
      "minDep" => "0.00000001"
      "minDepArrivalConfirm" => "19"
      "minFee" => "1"
      "minFeeForCtAddr" => "1"
      "minWd" => "2"
      "minWdUnlockConfirm" => "38"
      "name" => "Tether"
      "needTag" => false
      "usedDepQuotaFixed" => ""
      "usedWdQuota" => "0"
      "wdQuota" => "10000000"
      "wdTickSz" => "6"
    ]

     */
    protected function getWithdrawalFee($currency, $protocol)
    {
        $url = "{$this->baseUrl}/asset/currencies";

        $response = $this->sendRequest('GET', $url);

        if (isset($response['code']) && $response['code'] === '0') {
            foreach ($response['data'] as $currencyData) {
                if ($currencyData['ccy'] === strtoupper($currency)) {
                    $chain = str_replace($currency."-", "", $currencyData['chain']);

                    if (strtolower($chain) == strtolower($protocol))
                        return $currencyData['minFee'];
                }
            }
        }

        return '0.0';
    }

    public function transfer($currency, $amount, $fromAccount = "18", $toAccount = "6")
    {
        $url = "{$this->baseUrl}/asset/transfer";

        $params = [
            'ccy' => $currency,
            'amt' => (string)$amount,
            'from' => (string)$fromAccount, // Код аккаунта, с которого переводятся средства
            'to' => (string)$toAccount,     // Код аккаунта, на который переводятся средства
            // 'type' => '0', // Необязательный параметр, по умолчанию '0' для внутренних переводов
        ];

        $response = $this->sendRequest('POST', $url, $params);

        if (isset($response['code']) && $response['code'] === '0') {
            return [
                'status' => 'success',
                'data' => $response['data'][0],
            ];
        }

        return [
            'status' => 'error',
            'message' => $response['msg'] ?? 'Transfer failed.',
            'code' => $response['code'] ?? '',
        ];
    }






}

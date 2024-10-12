<?php

namespace App\Repositories\Exchanges;

interface ExchangeApiInterface
{
    public function getExchangeRate($fromCurrency, $toCurrency);

    public function createWallet($currency, $protocol);
}

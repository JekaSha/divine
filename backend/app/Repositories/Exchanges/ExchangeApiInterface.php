<?php

namespace App\Repositories\Exchanges;

interface ExchangeApiInterface
{
    public function getExchangeRate($fromCurrency, $toCurrency);
}

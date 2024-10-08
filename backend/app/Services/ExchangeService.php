<?php

namespace App\Services;

use App\Repositories\CurrencyRepository;
use App\Repositories\CurrencyProtocolRepository;
use App\Repositories\WalletRepository;
use App\Models\CurrencyExchange;
use App\Models\Currency;
use App\Services\ExchangeApiService;

class ExchangeService
{
    protected $currencyRepository;
    protected $protocolRepository;
    protected $walletRepository;
    protected $exchangeApiService;

    public function __construct(
        CurrencyRepository $currencyRepository,
        CurrencyProtocolRepository $protocolRepository,
        WalletRepository $walletRepository
    )
    {
        $this->currencyRepository = $currencyRepository;
        $this->protocolRepository = $protocolRepository;
        $this->walletRepository = $walletRepository;
    }

    public function getData(array $currencyFilters = [], array $protocolFilters = [])
    {
        $currencies = $this->currencyRepository->all($currencyFilters);
        $protocols = $this->protocolRepository->all($protocolFilters);

        return [
            'currencies' => $currencies,
            'protocols' => $protocols,
        ];
    }

    public function getAvailableCurrencies()
    {
        return $this->walletRepository->getAvailableCurrencies();
    }

    public function fetchAndSaveExchangeRate($fromCurrencyId, $toCurrencyId, $exchangeId)
    {
        $fromCurrency = Currency::find($fromCurrencyId);
        $toCurrency = Currency::find($toCurrencyId);

        $this->exchangeApiService = new ExchangeApiService($exchangeId);

        $rate = $this->exchangeApiService->getExchangeRate($fromCurrency->name, $toCurrency->name);

        if ($rate) {
            $currentRate = $rate ?? 0;

            // Сохраняем курс в базу данных
            CurrencyExchange::updateOrCreate(
                [
                    'from_currency_id' => $fromCurrencyId,
                    'to_currency_id' => $toCurrencyId,
                    'exchange_id' => $exchangeId,
                ],
                ['current_rate' => $currentRate]
            );

            return $currentRate;
        }

        return null; // В случае ошибки
    }
}

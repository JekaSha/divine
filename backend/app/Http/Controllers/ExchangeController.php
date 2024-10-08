<?php

namespace App\Http\Controllers;

use App\Services\ExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Wallet;


class ExchangeController extends Controller
{
    protected $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }


    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableCurrencies(): array
    {
        $currencies = $this->exchangeService->getAvailableCurrencies();

        $status = "error";

        if ($currencies instanceof Collection) {
            $status = "success";
        }
        $json = [
            'status' => $status,
            'data' => $currencies
        ];
        return $json;
    }

    /**
     *
     * @param Request $request
     * @param int $currencyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProtocolsByCurrency(Request $request, $currencyId):array
    {
        $protocols = $this->exchangeService->getProtocolsForCurrency($currencyId);

        return ['data' => $protocols];
    }

    public function getRate(Request $request) {
        $currencyId = $request->currency;
        $toCurrencyId = $request->to_currency;

        $wallet = Wallet::where('status', 'system')
            ->where('currency_id', $currencyId)
            ->first();

        if ($wallet && $currencyId && $toCurrencyId) {
            $exchangeId = $wallet->account->exchange_id;
            $rate = $this->exchangeService->fetchAndSaveExchangeRate($currencyId, $toCurrencyId, $exchangeId);
        }

        return $rate;
    }
}

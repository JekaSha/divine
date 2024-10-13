<?php

namespace App\Http\Controllers;

use App\Services\ExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Wallet;
use App\Http\Requests\Exchange\PostOrderRequest;


class ExchangeController extends Controller
{
    protected $exchangeService;
    protected $userId = 1;

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
        $amount = $request->amount;

        $wallet = $this->exchangeService->getWallets($currencyId, false, $this->userId)->first();

        $json = ['status' => 'error', 'msg' => 'No Active Wallet'];
        if ($wallet && $currencyId && $toCurrencyId) {
            $exchangeId = $wallet->account->exchange_id;
            $rate = $this->exchangeService->getExchangeRate($currencyId, $toCurrencyId, $exchangeId);
            $receivedAmount = $request->amount * $rate;
            $json = ['status' => 'success', "data" => [
                'receivedAmount' => $receivedAmount,
                'rate' => $rate
            ]];
        }


        return $json;
    }

    public function postOrder(PostOrderRequest $request) {

        $validatedData = $request->validated();

        $data = $this->exchangeService->postOrder($validatedData);

        return $data;
    }
}

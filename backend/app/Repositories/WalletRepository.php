<?php

namespace App\Repositories;

use App\Models\Wallet;

class WalletRepository
{
    /**
     *
     * @param int $accountId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableCurrencies($userId)
    {
        $currencies = Wallet::with(['currency', 'protocol']) // Загружаем валюты и протоколы
        ->where('status', 'system')
            ->where('user_id', $userId)
            ->get()
            ->groupBy('currency.id') // Группируем по ID валюты
            ->map(function ($group) {
                return [
                    'currency_id' => $group->first()->currency->id,
                    'currency_name' => $group->first()->currency->name,
                    'protocols' => $group->map(function ($item) {
                        return [
                            'protocol_id' => $item->protocol->id,
                            'protocol_name' => $item->protocol->name,
                        ];
                    })->unique('protocol_id'), // Уникальные протоколы по ID
                ];
            })
            ->values();

        return $currencies;
    }

    public function get($currencyId, $protocolId, $userId) {

        $wallets = Wallet::where('currency_id', $currencyId)
            ->where('status', 'active')
            ->where('protocol_id', $protocolId)
            ->where('user_id', $userId)
            ->get();

        return $wallets;
    }

}

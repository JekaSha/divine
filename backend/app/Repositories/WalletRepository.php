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
    public function getAvailableCurrencies()
    {
        $currencies = Wallet::with(['currency', 'protocol']) // Загружаем валюты и протоколы
        ->where('status', 'system')
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
}

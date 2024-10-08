<?php

namespace App\Repositories;

use App\Models\CurrencyProtocol;

class CurrencyProtocolRepository
{
    public function all(array $filters = [])
    {
        $query = CurrencyProtocol::query();

        // Применяем фильтры
        if (isset($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        return $query->get();
    }

    /**
     *
     * @param int $currencyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProtocolsByCurrencyId($currencyId)
    {
        return CurrencyProtocol::whereHas('currencies', function ($query) use ($currencyId) {
            $query->where('currencies.id', $currencyId);
        })->get();
    }
}

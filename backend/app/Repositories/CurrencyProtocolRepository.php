<?php

namespace App\Repositories;

use App\Models\CurrencyProtocol;

class CurrencyProtocolRepository
{
    public function all(array $filters = [])
    {
        $query = CurrencyProtocol::query();

        if (isset($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        if (isset($filters['name'])) {
            if (isset($filters['exact']) && $filters['exact']) {
                $query->where('name', $filters['name']);
            } else {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }
        }

        // Фильтр по ID
        if (isset($filters['id'])) {
            $query->whereIn('id', (array) $filters['id']); // Используем whereIn для возможности передачи нескольких ID
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

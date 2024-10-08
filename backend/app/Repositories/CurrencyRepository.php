<?php

namespace App\Repositories;

use App\Models\Currency;

class CurrencyRepository
{
    public function all(array $filters = [])
    {
        $query = Currency::query();

        // Применяем фильтры
        if (isset($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        return $query->get();
    }
}

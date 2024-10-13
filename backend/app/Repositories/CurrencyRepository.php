<?php

namespace App\Repositories;

use App\Models\Currency;

class CurrencyRepository
{
    public function all(array $filters = [])
    {
        $query = Currency::query();

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

        if (isset($filters['id'])) {
            $query->whereIn('id', (array) $filters['id']);
        }

        return $query->get();
    }


}

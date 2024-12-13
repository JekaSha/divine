<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\CurrencyRepository;

class InvoiceRepository
{
    protected CurrencyRepository $currencyRepository;
    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function get(array $filter = [])
    {
        $query = Invoice::query();

        if (!empty($filter['user_id'])) {
            $query->where('user_id', $filter['user_id']);
        }

        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }

        if (!empty($filter['hash'])) {
            $query->where('hash', $filter['hash']);
        }

        return $query->get();
    }

    public function store(array $data)
    {
        $defaultCurrency = env('DEFAULT_CURRENCY', 'USD');
        $total = 0;

        $currency = $defaultCurrency;

        foreach ($data['packages'] as $package) {
            if ($package['currency'] !== $defaultCurrency) {
                $convertedPrice = $this->currencyRepository->convert($package['price'], $package['currency'], $defaultCurrency);
            } else {
                $convertedPrice = $package['price'];
            }

            $total += $convertedPrice;
        }

        $data['total'] = $total;
        $data['currency'] = $currency;

        return Invoice::create($data);
    }
}

<?php

namespace App\Services;

use App\Repositories\CurrencyRepository;
use App\Repositories\CurrencyProtocolRepository;
use App\Models\CurrencyProtocol;

class CurrencyService
{
    protected $currencyRepository;
    protected $currencyProtocolRepository;

    public function __construct(CurrencyRepository $currencyRepository, CurrencyProtocolRepository $currencyProtocolRepository)
    {
        $this->currencyRepository = $currencyRepository;
        $this->currencyProtocolRepository = $currencyProtocolRepository;
    }

    /**
     */
    public function getAllCurrencies(array $filters = [])
    {
        return $this->currencyRepository->all($filters);
    }

    /**
     *
     * @param int $currencyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProtocolsForCurrency($currencyId)
    {
        return $this->currencyProtocolRepository->getProtocolsByCurrencyId($currencyId);
    }


    /**
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProtocols()
    {
        return $this->currencyProtocolRepository->all(); // Используем репозиторий для получения всех протоколов
    }
}

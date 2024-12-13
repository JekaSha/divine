<?php

namespace App\Services;

use App\Repositories\MerchantRepository;

class MerchantService
{
    protected $merchantRepository;

    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    public function get(array $data)
    {
        return $this->merchantRepository->get($data);
    }

    public function createPaymentLink($invoice, $merchant = "stripe") {

        $merchant = $this->get(['name' => $merchant]);
        if ($merchant) {
            $merchant = $merchant->first();
            $invoice->merchant_id = $merchant->id;
            $invoice->save();

            $url = $this->merchantRepository->createPaymentLink($invoice, $merchant);

            return $url;

        }

        return null;
    }
}


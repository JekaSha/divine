<?php

namespace App\Services\Merchants;

use App\Models\Merchant;
use App\Services\Merchants\StripeMerchant;

class MerchantFactory
{
    /**
     * Create an instance of the appropriate merchant class.
     *
     * @param string $merchant
     * @return mixed
     * @throws \Exception
     */
    public static function create(Merchant $merchant)
    {
        switch (strtolower($merchant->name)) {
            case 'stripe':
                $apiKey = trim($merchant->key);
                return new StripeMerchant($apiKey);

            // Add more merchants here as needed
            case 'paypal':
                // return new PayPalMerchant($apiKey);
                throw new \Exception('PayPalMerchant is not yet implemented.');

            default:
                throw new \Exception("Unsupported merchant: {$merchant}");
        }
    }
}

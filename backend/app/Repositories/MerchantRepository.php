<?php

namespace App\Repositories;

use App\Models\Merchant;
use App\Models\Invoice;
use App\Services\Merchants\MerchantFactory;

class MerchantRepository
{
    public function get(array $filter = [])
    {
        $query = Merchant::query();

        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }

        if (!empty($filter['name'])) {
            $query->where('name',  $filter['name']);
        }

        return $query->get();
    }

    public function createPaymentLink(Invoice $invoice, Merchant $merchant): string
    {
        // Get the merchant instance via the factory

        $merchantInstance = MerchantFactory::create($merchant);

        // Prepare invoice data
        $hash = explode("-", $invoice->hash)[0];
        $invoiceData = [
            'total' => $invoice->total,
            'currency' => $invoice->currency,
            'description' => "Payment for Invoice #{$hash}",
            'invoice_hash' => $invoice->hash,
            'lang' => $invoice->user->language,
        ];

        // Generate and return the payment link
        $url = $merchantInstance->createPaymentLink($invoiceData);
        return $url;
    }

    public function getByInvoiceId($invoiceId)
    {
        return Merchant::whereHas('invoices', function ($query) use ($invoiceId) {
            $query->where('id', $invoiceId);
        })->first();
    }
}

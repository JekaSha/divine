<?php


namespace App\Services\Merchants;

use Stripe\StripeClient;

class StripeMerchant
{
    protected $stripe;

    /**
     * Constructor to initialize Stripe Client
     *
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->stripe = new StripeClient($apiKey);
    }

    /**
     * Create a payment link for a given invoice
     *
     * @param array $invoiceData
     * @return string
     * @throws \Exception
     */
    public function createPaymentLink(array $invoiceData): string
    {
        try {
            // Create a product for the invoice if necessary
            $product = $this->stripe->products->create([
                'name' => $invoiceData['description'] ?? 'Invoice Payment',
            ]);

            // Create a price object based on the invoice amount
            $price = $this->stripe->prices->create([
                'unit_amount' => $invoiceData['total'] * 100, // Amount in cents
                'currency' => $invoiceData['currency'],
                'product' => $product->id,
            ]);

            // Create a payment link
            $paymentLink = $this->stripe->paymentLinks->create([
                'line_items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1,
                    ],
                ],
            ]);

            return $paymentLink->url;
        } catch (\Exception $e) {
            throw new \Exception('Error creating Stripe payment link: ' . $e->getMessage());
        }
    }
}

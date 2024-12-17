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

            $redirectLink = env('FRONTEND_HOST').'/payment/success';
            bb($redirectLink);
            // Create a payment link
            $paymentLink = $this->stripe->paymentLinks->create([
                'line_items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1,
                    ],
                ],

                'payment_intent_data' => ['metadata' => ['invoice_hash' => $invoiceData['invoice_hash']]],
                'metadata' => ['invoice_hash' => $invoiceData['invoice_hash']],

                "after_completion" => [
                    'type' => 'redirect',
                    'redirect' => [
                        'url' => $redirectLink,
                    ],
                ]
            ]);
bb($invoiceData);
            return $paymentLink->url;
        } catch (\Exception $e) {
            throw new \Exception('Error creating Stripe payment link: ' . $e->getMessage());
        }
    }
}

/*
 *
 *
 *
 */

/*
 * Created Link:
 *
 * Array
(
    [id] => evt_1QWlBMHOcioBkeK5CqtoU7L0
    [object] => event
    [api_version] => 2022-08-01
    [created] => 1734381984
    [data] => Array
        (
            [object] => Array
                (
                    [id] => plink_1QWlBMHOcioBkeK54qL0xJ6A
                    [object] => payment_link
                    [active] => 1
                    [after_completion] => Array
                        (
                            [hosted_confirmation] => Array
                                (
                                    [custom_message] =>
                                )

                            [type] => hosted_confirmation
                        )

                    [allow_promotion_codes] =>
                    [application] =>
                    [application_fee_amount] =>
                    [application_fee_percent] =>
                    [automatic_tax] => Array
                        (
                            [enabled] =>
                            [liability] =>
                        )

                    [billing_address_collection] => auto
                    [consent_collection] =>
                    [currency] => usd
                    [custom_fields] => Array
                        (
                        )

                    [custom_text] => Array
                        (
                            [after_submit] =>
                            [shipping_address] =>
                            [submit] =>
                            [terms_of_service_acceptance] =>
                        )

                    [customer_creation] => if_required
                    [inactive_message] =>
                    [invoice_creation] => Array
                        (
                            [enabled] =>
                            [invoice_data] => Array
                                (
                                    [account_tax_ids] =>
                                    [custom_fields] =>
                                    [description] =>
                                    [footer] =>
                                    [issuer] =>
                                    [metadata] => Array
                                        (
                                        )

                                    [rendering_options] =>
                                )

                        )

                    [livemode] =>
                    [metadata] => Array
                        (
                        )

                    [on_behalf_of] =>
                    [payment_intent_data] =>
                    [payment_method_collection] => always
                    [payment_method_types] =>
                    [phone_number_collection] => Array
                        (
                            [enabled] =>
                        )

                    [restrictions] =>
                    [shipping_address_collection] =>
                    [shipping_options] => Array
                        (
                        )

                    [submit_type] => auto
                    [subscription_data] =>
                    [tax_id_collection] => Array
                        (
                            [enabled] =>
                            [required] => never
                        )

                    [transfer_data] =>
                    [url] => https://buy.stripe.com/test_00gcOt9Rn1jf1sQ3cj
                )

        )

    [livemode] =>
    [pending_webhooks] => 4
    [request] => Array
        (
            [id] => req_0mbqzQObdKlMwh
            [idempotency_key] => f712c8f2-09a4-4e6b-8e4e-fdc61503b407
        )

    [type] => payment_link.created
)
 *
 */

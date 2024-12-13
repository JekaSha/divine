<?php

namespace App\Http\Controllers;

use App\Events\PaymentProcessed;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Event;
use App\Repositories\MerchantRepository;
use App\Repositories\InvoiceRepository;
use App\Services\UserService;
use App\Services\PermissionService;

class WebhookController extends Controller
{
    protected $merchantRepository;
    protected $invoiceRepository;
    protected $userService;
    protected $permissionService;

    public function __construct(
        Request $request,
        MerchantRepository $merchantRepository,
        InvoiceRepository $invoiceRepository,
        UserService $userService,
        PermissionService $permissionService
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->userService = $userService;
        $this->permissionService = $permissionService;
    }

    public function stripe(Request $request)
    {
        bb('webhook');

        $signature = $request->header('Stripe-Signature');
        $merchant = $this->merchantRepository->get(['name' => 'stripe'])->first();
        $endpointSecret = $merchant->key;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $signature,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->updateInvoiceStatus($event->data->object, 'paid');
                break;

            case 'charge.failed':
                $this->updateInvoiceStatus($event->data->object, 'declined');
                break;

            default:
                return response()->json(['error' => 'Unhandled event type'], 400);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Update the status of the invoice based on the event metadata.
     *
     * @param object $paymentIntent
     * @param string $status
     */
    private function updateInvoiceStatus($paymentIntent, string $status)
    {
        $metadata = $paymentIntent->metadata ?? null;
        $invoiceHash = $metadata->invoice_hash ?? null;
        bb("payment processed: {$invoiceHash} = " . $status);

        if (!$invoiceHash) {
            bb("Invoice hash not found in payment metadata.");
            return;
        }

        $invoice = $this->invoiceRepository->get(['hash' => $invoiceHash])->first();

        if (!$invoice) {
            bb("Invoice not found for hash: {$invoiceHash}");
            return;
        }

        // Update invoice status
        $invoice->status = $status;
        $invoice->save();

        // Use PermissionService to manage user permissions
        $this->permissionService = new PermissionService($this->userService, $invoice->user_id);

        if ($invoice->packages) {
            foreach ($invoice->packages as $package) {
                if ($package['type'] === "requests_per_month") {
                    $this->permissionService->extendPackage($package['days']);
                    $this->permissionService->addRequests($package['requests']);
                }
            }
        }

        // Trigger the PaymentProcessed event
        event(new PaymentProcessed($invoice));
    }
}

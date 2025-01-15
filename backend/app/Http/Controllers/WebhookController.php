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
    protected $debug = false;

    protected $invoice;
    public function __construct(
        Request $request,
        MerchantRepository $merchantRepository,
        InvoiceRepository $invoiceRepository,
        UserService $userService

    ) {

       // bb('construct start: '. $request->path());
      //  bb($request->all());
        //$request->data['object']['metadata']['invoice_hash'] = "da2205e9-763d-4ff4-acb8-e8643b599bfc";
        $this->merchantRepository = $merchantRepository;
        $this->invoiceRepository = $invoiceRepository;

        if (!$this->debug) {
            $invoiceHash = @$request->data['object']['metadata']['invoice_hash'];
        } else {
            $invoiceHash = "da2205e9-763d-4ff4-acb8-e8643b599bfc";
        }

        if ($invoiceHash) {
            $this->invoice = $this->invoiceRepository->get(['hash' => $invoiceHash])->first();
            $this->userService = $userService;
            bb($this->invoice->user_id);
            $this->userService->setUserId($this->invoice->user_id);

            $user = $this->userService->get(['id' => $this->userService->getUserId()]);
            if ($user) {
                $user = $user->first();
                $request->token = $user->remember_token;
                parent::__construct($request);
            }


        }
    //    bb('end const');
    }

    public function stripe(Request $request)
    {

        $signature = $request->header('Stripe-Signature');

        $merchant = $this->merchantRepository->get(['name' => 'stripe'])->first();
        $endpointSecret = trim($merchant->stream['webhook_sign']);


        try {
            bb('string test sign');

            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $signature,
                $endpointSecret
            );

        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 401);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 402);
        }
//bb($event->data->object);

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

        if ($this->invoice && in_array($this->invoice->status, ['pending', 'waiting'])) {
            $metadata = $paymentIntent->metadata ?? null;
            $invoiceHash = $metadata->invoice_hash ?? null;

            // Update invoice status
            $this->invoice->response = $paymentIntent;
            $this->invoice->status = $status;
            $this->invoice->save();

            // Use PermissionService to manage user permissions

           /// $this->permissionService = new PermissionService($this->userService);

            if ($this->invoice->packages) {
                foreach ($this->invoice->packages as $package) {
                    $this->userService->handlePackage($package);
                }
            }
            // Trigger the PaymentProcessed event
            event(new PaymentProcessed($this->invoice));
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InvoiceService;
use App\Services\MerchantService;

class InvoiceController extends Controller
{
    protected $invoiceService;
    protected $merchantService;

    public function __construct(
        Request $request,
        InvoiceService $invoiceService,
        MerchantService $merchantService
    )
    {
        parent::__construct($request);
        $this->invoiceService = $invoiceService;
        $this->merchantService = $merchantService;


    }

    public function create(Request $request)
    {
        try {

            $invoice = $this->invoiceService->create($this->user['id'], $request);
            $r = ['status' => 'success', 'data' => ['invoice' => $invoice]];
            return $r;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }


    }

    public function show($invoiceId)
    {
        $invoice = $this->invoiceService->get(['id' => $invoiceId]);

        if (!$invoice) {
            abort(404, 'Invoice not found');
        }

        return view('invoice.show', ['invoice' => $invoice]);
    }

    public function merchant($invoiceHash) {

        $invoice = $this->invoiceService->get(['hash' => $invoiceHash]);

        $url = $this->merchantService->createPaymentLink($invoice, "stripe");

        return redirect($url);
    }

}

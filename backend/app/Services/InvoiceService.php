<?php

namespace App\Services;

use App\Repositories\InvoiceRepository;
use App\Repositories\PackageRepository;
use Illuminate\Support\Str;

class InvoiceService
{
    protected $invoiceRepository;
    protected $packageRepository;

    public function __construct(InvoiceRepository $invoiceRepository, PackageRepository $packageRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->packageRepository = $packageRepository;
    }

    public function create($userId, $request)
    {

        $packageId = $request->input('packageId');

        // Fetch the package details
        $package = $this->packageRepository->get(['id' => $packageId])->first();

        if (!$package) {
            throw new \Exception('Package not found.');
        }

        // Prepare data for the invoice
        $invoiceData = [
            'user_id' => $userId,
            'packages' => [$package->toArray()],
            'status' => 'pending',
        ];


        $invoice = $this->invoiceRepository->store($invoiceData);
        return $invoice;
    }

    public function get(array $filter = [])
    {
        return $this->invoiceRepository->get($filter)->first();
    }
}

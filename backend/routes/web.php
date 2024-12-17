<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\InvoiceController;
Route::get('/r/invoice/{invoiceHash}', [InvoiceController::class, 'merchant'])->name('invoice.merchant');

use App\Http\Controllers\WebhookController;
Route::any('/webhooks/stripe', [WebhookController::class, 'stripe']);


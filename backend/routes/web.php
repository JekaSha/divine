<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\InvoiceController;
Route::get('/r/invoice/{invoiceHash}', [InvoiceController::class, 'merchant'])->name('invoice.merchant');

Route::post('/webhooks/stripe', [WebhookController::class, 'handle']);

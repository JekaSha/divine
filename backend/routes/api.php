<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\ExchangeController;
Route::get('/exchange/getAllCurrencies', [ExchangeController::class, 'getAvailableCurrencies']);
Route::get('/exchange/rate', [ExchangeController::class, 'getRate']);

Route::get('/exchange/order', [ExchangeController::class, 'postOrder']);



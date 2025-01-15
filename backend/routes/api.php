<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChallengeController;

use App\Http\Controllers\InvoiceController;

//die('333');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/challenges/get', [ChallengeController::class, 'get']);
Route::any('/challenges/session/{session_hash}', [ChallengeController::class, 'getSession']);
Route::any('/challenges/answer/{session_hash}', [ChallengeController::class, 'request']);
Route::any('/challenges/get/answer/{id}', [ChallengeController::class, 'get']);
Route::any('/challenges/packages/', [InvoiceController::class, 'getPackages']);

Route::any('/challenges/sendToEmail/{session_hash}', [ChallengeController::class, 'sendToEmail']);

Route::get('/auth/validate', [ChallengeController::class, 'validateToken']);

Route::any('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');



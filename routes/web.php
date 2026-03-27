<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

Route::get('/', [PaymentController::class, 'index']);
Route::post('/pay', [PaymentController::class, 'create']);

Route::get('/payment/success', function () {
    return view('payment.success');
});

Route::get('/payment/{id}', [PaymentController::class, 'show']);
Route::get('/payment/{id}/status', [PaymentController::class, 'status']);
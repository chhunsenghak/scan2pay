<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PaymentController;

Route::get('/', [PaymentController::class, 'index'])->name('payment.index');

Route::post('/pay', [PaymentController::class, 'create'])->name('payment.create');

Route::get('/payment/show', [PaymentController::class, 'show'])->name('payment.show');

Route::post('/payment/status', [PaymentController::class, 'status'])->name('payment.status');

Route::post('/bakong/webhook', [PaymentController::class, 'webhook']);

Route::get('/payment/success', function () {
    return view('payment.success');
});
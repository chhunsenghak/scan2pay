<?php 

use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('/payment')->group(function () {
    Route::post('/request', [PaymentController::class, 'processPayment']);
    Route::post('/verify', [PaymentController::class, 'paymentStatus']);
    Route::post('/renew-token', [PaymentController::class, 'renewToken']);

    Route::post('/generate-deeplink', [PaymentController::class, 'generateDeeplink']);
    Route::post('/check/md5', [PaymentController::class, 'checkMD5']);
    Route::post('/check/hash', [PaymentController::class, 'checkHash']);
    Route::post('/check/short-hash', [PaymentController::class, 'checkShortHash']);
});

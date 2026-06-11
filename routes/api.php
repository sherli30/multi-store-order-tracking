<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/profile/update', [App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
    Route::get('/orders', [App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::post('/orders', [App\Http\Controllers\Api\OrderController::class, 'store']);
    Route::post('/orders/{id}/complete', [App\Http\Controllers\Api\OrderController::class, 'complete']);

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    Route::patch('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/scan/{identifier}', [App\Http\Controllers\Api\OrderController::class, 'scan']);

        // Webhook Recovery Routes
        Route::prefix('recovery')->group(function () {
            Route::get('/failed-orders', [\App\Http\Controllers\WebhookRecoveryController::class, 'failedOrders']);
            Route::get('/audit-all', [\App\Http\Controllers\WebhookRecoveryController::class, 'auditAllOrders']);
            Route::get('/audit/{orderId}', [\App\Http\Controllers\WebhookRecoveryController::class, 'auditOrder']);
            Route::post('/recover-payment/{orderId}', [\App\Http\Controllers\WebhookRecoveryController::class, 'recoverPaymentStock']);
            Route::post('/regenerate-token/{orderId}', [\App\Http\Controllers\WebhookRecoveryController::class, 'regenerateSnapToken']);
            Route::post('/resolve-failure/{failureId}', [\App\Http\Controllers\WebhookRecoveryController::class, 'markFailureResolved']);
        });
    });

    // Authenticated Payment Routes
    Route::post('/payment/snap-token', [App\Http\Controllers\Api\PaymentController::class, 'getSnapToken']);
    Route::get('/payment/status/{id}', [App\Http\Controllers\Api\PaymentController::class, 'checkStatus']);
});

// API Untuk Aplikasi Flutter
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/forgot-password', [App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
Route::get('/stores', [App\Http\Controllers\Api\StoreController::class, 'index']);
Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::post('/shipping/calculate', [App\Http\Controllers\Api\ShippingController::class, 'calculate']);
Route::get('/shipping/provinces', [App\Http\Controllers\Api\ShippingController::class, 'getProvinces']);
Route::get('/shipping/cities/{province_id}', [App\Http\Controllers\Api\ShippingController::class, 'getCities']);

// Webhook Payment Routes (Unauthenticated)
Route::post('/midtrans/callback', [\App\Http\Controllers\MidtransWebhookController::class, 'handle']);
Route::post('/payment/callback', [\App\Http\Controllers\MidtransWebhookController::class, 'handle']);

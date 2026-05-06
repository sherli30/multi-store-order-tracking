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
});

// API Untuk Aplikasi Flutter
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/forgot-password', [App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
Route::get('/stores', [App\Http\Controllers\Api\StoreController::class, 'index']);
Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::post('/shipping/calculate', [App\Http\Controllers\Api\ShippingController::class, 'calculate']);

// Payment Routes
Route::post('/payment/snap-token', [App\Http\Controllers\Api\PaymentController::class, 'getSnapToken']);
Route::post('/midtrans/callback', [App\Http\Controllers\Api\PaymentController::class, 'callback']);
Route::post('/payment/callback', [App\Http\Controllers\Api\PaymentController::class, 'callback']);
Route::get('/payment/status/{id}', [App\Http\Controllers\Api\PaymentController::class, 'checkStatus']);

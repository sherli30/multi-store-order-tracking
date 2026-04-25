<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Profile

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Toko & Produk
    Route::resource('stores', StoreController::class);
    Route::resource('products', ProductController::class)->scoped([
        'product' => 'slug',
    ]);
    Route::resource('product-categories', ProductCategoryController::class);

    // Returns active categories for a specific store as JSON.
    // Used by the product create/edit forms to populate the category dropdown
    // after the admin selects a store, enforcing the Store → Category scoping on the UI.
    Route::get('stores/{store}/categories', [ProductCategoryController::class, 'byStore'])
        ->name('stores.categories');

    // ── Stock Management ──────────────────────────────────────────────────
    // Manual stock add / deduct for a product (resolved by slug).
    // Separate from product update — every movement is logged in stock_movements.
    Route::prefix('products/{product:slug}/stock')->name('products.stock.')->group(function () {
        Route::get('/',       [StockController::class, 'index'])  ->name('index');   // history
        Route::post('/add',   [StockController::class, 'add'])    ->name('add');     // type: in
        Route::post('/deduct',[StockController::class, 'deduct']) ->name('deduct'); // type: out
    });

    // Pesanan (Orders)
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('orders.update-shipping');

    /// Daftar semua transaksi (dengan filter & tab)
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    // Detail satu transaksi
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->name('transactions.show');

    // Update status (konfirmasi / tolak / refund)
    Route::patch('/transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])
        ->name('transactions.updateStatus');

    // Deliveries & Tracking
    Route::get('/deliveries', [App\Http\Controllers\DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/scan', [App\Http\Controllers\DeliveryController::class, 'scan'])->name('deliveries.scan');
    Route::post('/deliveries/update-tracking', [App\Http\Controllers\DeliveryController::class, 'updateTracking'])->name('deliveries.updateTracking');
    Route::get('/deliveries/history', [App\Http\Controllers\DeliveryController::class, 'history'])->name('deliveries.history');
    Route::get('/deliveries/{order}/label', [App\Http\Controllers\DeliveryController::class, 'printLabel'])->name('deliveries.label');

    // Manajemen Customer
    Route::resource('customers', CustomerController::class)
        ->only(['index', 'show', 'destroy']);

    Route::patch('customers/{customer}/status', [CustomerController::class, 'updateStatus'])
        ->name('customers.update-status');
    // Laporan Penjualan
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/stores', [App\Http\Controllers\ReportController::class, 'stores'])->name('reports.stores');
    Route::get('/reports/consolidated', [App\Http\Controllers\ReportController::class, 'consolidated'])->name('reports.consolidated');
    Route::get('/reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
});

require __DIR__ . '/auth.php';

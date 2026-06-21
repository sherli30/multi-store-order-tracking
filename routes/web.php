<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ShippingServiceController;
use App\Http\Controllers\ShippingRateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:administrator,logistics', 'prevent.back.history'])->group(function () {
    // Profile

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/stream', [NotificationController::class, 'stream'])->name('notifications.stream');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::get('/notifications/{id}/redirect', [NotificationController::class, 'redirect'])->name('notifications.redirect');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Toko & Produk
    Route::resource('stores', StoreController::class);
    Route::patch('stores/{store}/status', [StoreController::class, 'updateStatus'])->name('stores.update-status');
    Route::delete('stores/{store}/logo', [StoreController::class, 'destroyLogo'])->name('stores.destroyLogo');
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/status', [ProductController::class, 'updateStatus'])->name('products.update-status');
    Route::delete('products/images/{image}', [ProductController::class, 'destroyImage'])->name('products.destroyImage');
    Route::resource('product-categories', ProductCategoryController::class);
    Route::patch('product-categories/{product_category}/status', [ProductCategoryController::class, 'updateStatus'])->name('product-categories.update-status');

    // Returns active categories for a specific store as JSON.
    // Used by the product create/edit forms to populate the category dropdown
    // after the admin selects a store, enforcing the Store → Category scoping on the UI.
    Route::get('stores/{store}/categories', [ProductCategoryController::class, 'byStore'])
        ->name('stores.categories');

    // ── Stock Management ──────────────────────────────────────────────────
    // Manual stock add / deduct for a product (resolved by slug).
    // Separate from product update — every movement is logged in stock_movements.
    Route::prefix('products/{product}/stock')->name('products.stock.')->group(function () {
        Route::get('/',       [StockController::class, 'index'])->name('index');   // history
        Route::post('/add',   [StockController::class, 'add'])->name('add');     // type: in
        Route::post('/deduct', [StockController::class, 'deduct'])->name('deduct'); // type: out
    });

    // Pesanan (Orders)
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/print', [OrderController::class, 'printShippingLabel'])->name('orders.print');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('orders.update-shipping');
    Route::patch('orders/{order}/tracking', [OrderController::class, 'updateTrackingNumber'])->name('orders.update-tracking-number');
    Route::post('orders/{order}/generate-resi', [OrderController::class, 'generateResi'])->name('orders.generate-resi');
    Route::patch('orders/{order}/handle-return', [OrderController::class, 'handleReturn'])->name('orders.handle-return');

    /// Daftar semua transaksi (dengan filter & tab)
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    // Detail satu transaksi
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->name('transactions.show');


    // Deliveries & Tracking
    Route::get('/deliveries', [App\Http\Controllers\DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/scan', [App\Http\Controllers\DeliveryController::class, 'scan'])->name('deliveries.scan');
    Route::get('/deliveries/history', [App\Http\Controllers\DeliveryController::class, 'history'])->name('deliveries.history');
    Route::get('/deliveries/{order}/tracking-modal', [App\Http\Controllers\DeliveryController::class, 'getTrackingModal'])->name('deliveries.tracking-modal');
    Route::get('/deliveries/{order}/label', [App\Http\Controllers\DeliveryController::class, 'printLabel'])->name('deliveries.label');

    // Manajemen Customer
    Route::resource('customers', CustomerController::class)
        ->only(['index', 'show', 'destroy']);

    Route::patch('customers/{customer}/status', [CustomerController::class, 'updateStatus'])
        ->name('customers.update-status');


    // Manajemen Kurir
    Route::resource('couriers', App\Http\Controllers\CourierController::class);
    Route::patch('couriers/{courier}/status', [App\Http\Controllers\CourierController::class, 'updateStatus'])->name('couriers.update-status');
    Route::resource('shipping-services', ShippingServiceController::class);
    Route::patch('shipping-services/{shipping_service}/status', [ShippingServiceController::class, 'updateStatus'])->name('shipping-services.update-status');
    Route::patch('shipping-rates/{shipping_rate}/status', [ShippingRateController::class, 'updateStatus'])->name('shipping-rates.update-status');
    Route::resource('shipping-rates', ShippingRateController::class);

    // Manajemen Wilayah
    Route::resource('provinces', ProvinceController::class);
    Route::patch('provinces/{province}/status', [ProvinceController::class, 'updateStatus'])->name('provinces.update-status');
    
    Route::resource('cities', CityController::class);
    Route::patch('cities/{city}/status', [CityController::class, 'updateStatus'])->name('cities.update-status');

    // Audit Trails & Logging
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/admin-actions', [AuditLogController::class, 'adminActions'])->name('admin-actions');
        Route::get('/admin-actions/export', [AuditLogController::class, 'exportAdminActions'])->name('admin-actions.export');
        Route::get('/orders/{order}/tracking', [AuditLogController::class, 'orderTracking'])->name('order-tracking');
        Route::get('/entity/{entityType}/{entityId}', [AuditLogController::class, 'entityHistory'])->name('entity-history');
    });

    // Laporan Penjualan
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
});

// ── Midtrans Webhook ─────────────────────────────────────────────────────
// This route MUST be outside auth middleware. Midtrans POSTs here server-to-
// server after any payment event. CSRF is exempted in VerifyCsrfToken.php.
Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle'])
    ->name('midtrans.callback');

require __DIR__ . '/auth.php';

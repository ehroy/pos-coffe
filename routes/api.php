<?php

use App\Http\Controllers\Api\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Api\Admin\StockController as AdminStockController;
use App\Http\Controllers\Api\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Api\Cashier\ProductController as CashierProductController;
use App\Http\Controllers\Api\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\Kitchen\OrderController as KitchenOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('customer')->name('api.customer.')->group(function () {
    Route::get('/tables/{qr_token}/menu', [CustomerMenuController::class, 'show'])->name('menu');
    Route::post('/tables/{qr_token}/orders', [CustomerOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/status', [CustomerOrderController::class, 'status'])->name('orders.status');
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['role:cashier,admin,owner'])->prefix('cashier')->name('api.cashier.')->group(function () {
        Route::get('/products', [CashierProductController::class, 'index'])->name('products');
        Route::get('/orders', [CashierOrderController::class, 'index'])->name('orders');
        Route::post('/orders', [CashierOrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/pay', [CashierOrderController::class, 'pay'])->name('orders.pay');
        Route::patch('/orders/{order}/status', [CashierOrderController::class, 'updateStatus'])->name('orders.status');
    });

    Route::middleware(['role:kitchen,admin,owner'])->prefix('kitchen')->name('api.kitchen.')->group(function () {
        Route::get('/orders', [KitchenOrderController::class, 'index'])->name('orders');
        Route::patch('/orders/{order}/status', [KitchenOrderController::class, 'update'])->name('orders.status');
    });

    Route::middleware(['role:admin,owner'])->prefix('admin')->name('api.admin.')->group(function () {
        Route::get('/reports/sales', [AdminReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/profit', [AdminReportController::class, 'profit'])->name('reports.profit');
        Route::get('/reports/expenses', [AdminReportController::class, 'expenses'])->name('reports.expenses');
        Route::get('/stock/low', [AdminStockController::class, 'lowStock'])->name('stock.low');
        Route::post('/stock/in', [AdminStockController::class, 'stockIn'])->name('stock.in');
        Route::post('/stock/out', [AdminStockController::class, 'stockOut'])->name('stock.out');
        Route::post('/stock/adjustment', [AdminStockController::class, 'adjustment'])->name('stock.adjustment');
    });
});

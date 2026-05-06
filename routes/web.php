<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ProductController as OwnerProductController;
use App\Http\Controllers\Owner\InventoryController as OwnerInventoryController;
use App\Http\Controllers\Owner\TableController as OwnerTableController;
use App\Http\Controllers\Owner\UserController as OwnerUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Http\Controllers\Kitchen\DashboardController as KitchenDashboardController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Protected routes
Route::middleware(['auth', 'active.user'])->group(function () {
    
    // Profile routes (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Owner routes
    Route::middleware(['role:owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/products', [OwnerProductController::class, 'index'])->name('products');
        Route::get('/inventory', [OwnerInventoryController::class, 'index'])->name('inventory');
        Route::get('/tables', [OwnerTableController::class, 'index'])->name('tables');
        Route::get('/users', [OwnerUserController::class, 'index'])->name('users');
    });

    // Admin routes (owner juga bisa akses)
    Route::middleware(['role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/products', [AdminProductController::class, 'index'])->name('products');
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories');
        Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory');
    });

    // Cashier routes
    Route::middleware(['role:cashier,admin,owner'])->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');
    });

    // Kitchen routes
    Route::middleware(['role:kitchen,admin,owner'])->prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/dashboard', [KitchenDashboardController::class, 'index'])->name('dashboard');
    });
});

require __DIR__.'/auth.php';

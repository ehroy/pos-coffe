<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ProductController as OwnerProductController;
use App\Http\Controllers\Admin\ProductVariantController as OwnerProductVariantController;
use App\Http\Controllers\Owner\InventoryController as OwnerInventoryController;
use App\Http\Controllers\Owner\TableController as OwnerTableController;
use App\Http\Controllers\Owner\UserController as OwnerUserController;
use App\Http\Controllers\Owner\SettingController as OwnerSettingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\TableController as AdminTableController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\QrCodeController as AdminQrCodeController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\RecipeController as AdminRecipeController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Http\Controllers\Cashier\PosController as CashierPosController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Cashier\CashSessionController as CashierCashSessionController;
use App\Http\Controllers\Cashier\ReceiptController as CashierReceiptController;
use App\Http\Controllers\Kitchen\DashboardController as KitchenDashboardController;
use App\Http\Controllers\Kitchen\OrderController as KitchenOrderController;
use App\Http\Controllers\Kitchen\HistoryController as KitchenHistoryController;
use App\Http\Controllers\Warehouse\DashboardController as WarehouseDashboardController;
use App\Http\Controllers\Warehouse\InventoryController as WarehouseInventoryController;
use App\Http\Controllers\Warehouse\HistoryController as WarehouseHistoryController;
use App\Http\Controllers\Customer\CartController as CustomerCartController;
use App\Http\Controllers\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return inertia('Welcome');
});

Route::middleware(['auth', 'active.user'])->get('/dashboard', function () {
    $user = request()->user();

    return match ($user?->role) {
        'owner'     => redirect()->route('owner.dashboard'),
        'admin'     => redirect()->route('admin.dashboard'),
        'cashier'   => redirect()->route('cashier.dashboard'),
        'kitchen'   => redirect()->route('kitchen.dashboard'),
        'warehouse' => redirect()->route('warehouse.dashboard'),
        default     => redirect()->route('owner.dashboard'),
    };
})->name('dashboard');

// Customer QR table routes
Route::get('/table/{qr_token}', [CustomerMenuController::class, 'show'])->name('customer.table.menu');
Route::post('/table/{qr_token}/orders', [CustomerMenuController::class, 'store'])->name('customer.table.orders.store');
Route::get('/table/{qr_token}/cart', [CustomerCartController::class, 'cart'])->name('customer.table.cart');
Route::get('/table/{qr_token}/checkout', [CustomerCartController::class, 'checkout'])->name('customer.table.checkout');
Route::get('/customer/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
Route::get('/customer/orders/{order}/status', [CustomerOrderController::class, 'status'])->name('customer.orders.status');

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
        Route::post('/products', [OwnerProductController::class, 'store'])->name('products.store');
        Route::post('/products/{product}', [OwnerProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}', [OwnerProductController::class, 'update']);
        Route::post('/products/{product}/variants', [OwnerProductVariantController::class, 'store'])->name('products.variants.store');
        Route::patch('/products/{product}/variants/{variant}', [OwnerProductVariantController::class, 'update'])->name('products.variants.update');
        Route::delete('/products/{product}/variants/{variant}', [OwnerProductVariantController::class, 'destroy'])->name('products.variants.destroy');
        Route::get('/inventory', [OwnerInventoryController::class, 'index'])->name('inventory');
        Route::get('/reports', fn () => redirect()->route('admin.reports'))->name('reports');
        Route::get('/expenses', fn () => redirect()->route('admin.expenses'))->name('expenses');
        Route::get('/settings', [OwnerSettingController::class, 'index'])->name('settings');
        Route::patch('/settings', [OwnerSettingController::class, 'update'])->name('settings.update');
        Route::patch('/settings/customer-online-payment', [OwnerSettingController::class, 'updateCustomerOnlinePayment'])->name('settings.customer-online-payment');
        Route::post('/inventory/ingredients', [OwnerInventoryController::class, 'storeIngredient'])->name('inventory.ingredients.store');
        Route::post('/inventory/stock-in', [OwnerInventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('/inventory/stock-out', [OwnerInventoryController::class, 'stockOut'])->name('inventory.stock-out');
        Route::post('/inventory/adjustment', [OwnerInventoryController::class, 'adjustment'])->name('inventory.adjustment');
        Route::get('/tables', [OwnerTableController::class, 'index'])->name('tables');
        Route::post('/tables', [OwnerTableController::class, 'store'])->name('tables.store');
        Route::patch('/tables/{table}/toggle', [OwnerTableController::class, 'toggleActive'])->name('tables.toggle');
        Route::get('/users', [OwnerUserController::class, 'index'])->name('users');
        Route::post('/users', [OwnerUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/toggle', [OwnerUserController::class, 'toggleActive'])->name('users.toggle');
    });

    // Admin routes (owner juga bisa akses)
    Route::middleware(['role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/products', [AdminProductController::class, 'index'])->name('products');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::post('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}', [AdminProductController::class, 'update']);
        Route::post('/products/{product}/variants', [AdminProductVariantController::class, 'store'])->name('products.variants.store');
        Route::patch('/products/{product}/variants/{variant}', [AdminProductVariantController::class, 'update'])->name('products.variants.update');
        Route::delete('/products/{product}/variants/{variant}', [AdminProductVariantController::class, 'destroy'])->name('products.variants.destroy');
        Route::get('/recipes', [AdminRecipeController::class, 'index'])->name('recipes');
        Route::post('/recipes/{product}', [AdminRecipeController::class, 'store'])->name('recipes.store');
        Route::patch('/recipes/{product}/{recipe}', [AdminRecipeController::class, 'update'])->name('recipes.update');
        Route::delete('/recipes/{product}/{recipe}', [AdminRecipeController::class, 'destroy'])->name('recipes.destroy');
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::patch('/categories/{category}/toggle', [AdminCategoryController::class, 'toggleActive'])->name('categories.toggle');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory');
        Route::post('/inventory/ingredients', [AdminInventoryController::class, 'storeIngredient'])->name('inventory.ingredients.store');
        Route::post('/inventory/stock-in', [AdminInventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('/inventory/stock-out', [AdminInventoryController::class, 'stockOut'])->name('inventory.stock-out');
        Route::post('/inventory/adjustment', [AdminInventoryController::class, 'adjustment'])->name('inventory.adjustment');
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
        Route::get('/expenses', [AdminExpenseController::class, 'index'])->name('expenses');
        Route::post('/expenses', [AdminExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/tables', [AdminTableController::class, 'index'])->name('tables');
        Route::post('/tables', [AdminTableController::class, 'store'])->name('tables.store');
        Route::patch('/tables/{table}', [AdminTableController::class, 'update'])->name('tables.update');
        Route::patch('/tables/{table}/toggle', [AdminTableController::class, 'toggleActive'])->name('tables.toggle');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/toggle', [AdminUserController::class, 'toggleActive'])->name('users.toggle');
        Route::get('/export/sales/pdf', [AdminExportController::class, 'salesPdf'])->name('export.sales.pdf');
        Route::get('/export/sales/csv', [AdminExportController::class, 'salesCsv'])->name('export.sales.csv');
        Route::get('/export/expenses/csv', [AdminExportController::class, 'expensesCsv'])->name('export.expenses.csv');
        Route::get('/qr/{table}', [AdminQrCodeController::class, 'show'])->name('qr.show');
        Route::get('/qr/{table}/pdf', [AdminQrCodeController::class, 'pdf'])->name('qr.pdf');
        Route::get('/qr/bulk/pdf', [AdminQrCodeController::class, 'bulkPdf'])->name('qr.bulk');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::patch('/settings/customer-online-payment', [AdminSettingController::class, 'updateCustomerOnlinePayment'])->name('settings.customer-online-payment');
    });

    // Cashier routes
    Route::middleware(['role:cashier,admin,owner'])->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');
        Route::get('/pos', [CashierPosController::class, 'index'])->name('pos');
        Route::get('/orders', [CashierOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [CashierOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/payment', [CashierOrderController::class, 'payment'])->name('orders.payment');
        Route::post('/orders/{order}/pay', [CashierOrderController::class, 'pay'])->name('orders.pay');
        Route::post('/orders', [CashierOrderController::class, 'store'])->name('orders.store');
        Route::patch('/orders/{order}/approve', [CashierOrderController::class, 'approve'])->name('orders.approve');
        Route::patch('/orders/{order}/reject', [CashierOrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/confirm-payment', [CashierOrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
        Route::get('/cash-session', [CashierCashSessionController::class, 'index'])->name('cash-session');
        Route::post('/cash-session/open', [CashierCashSessionController::class, 'open'])->name('cash-session.open');
        Route::post('/cash-session/close', [CashierCashSessionController::class, 'close'])->name('cash-session.close');
        Route::get('/orders/{order}/receipt', [CashierReceiptController::class, 'html'])->name('orders.receipt');
        Route::get('/orders/{order}/receipt/pdf', [CashierReceiptController::class, 'pdf'])->name('orders.receipt.pdf');
    });

    // Kitchen routes
    Route::middleware(['role:kitchen,admin,owner'])->prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/dashboard', [KitchenDashboardController::class, 'index'])->name('dashboard');
        Route::get('/display', [KitchenOrderController::class, 'display'])->name('display');
        Route::get('/orders', [KitchenOrderController::class, 'index'])->name('orders');
        Route::patch('/orders/{order}', [KitchenOrderController::class, 'update'])->name('orders.update');
        Route::get('/history', [KitchenHistoryController::class, 'index'])->name('history');
    });

    // Warehouse routes
    Route::middleware(['role:warehouse,admin,owner'])->prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/dashboard', [WarehouseDashboardController::class, 'index'])->name('dashboard');
        Route::get('/inventory', [WarehouseInventoryController::class, 'index'])->name('inventory');
        Route::post('/inventory/ingredients', [WarehouseInventoryController::class, 'storeIngredient'])->name('inventory.ingredients.store');
        Route::post('/stock-in', [WarehouseInventoryController::class, 'stockIn'])->name('stock-in');
        Route::post('/stock-out', [WarehouseInventoryController::class, 'stockOut'])->name('stock-out');
        Route::post('/adjustment', [WarehouseInventoryController::class, 'adjustment'])->name('adjustment');
        Route::get('/history', [WarehouseHistoryController::class, 'index'])->name('history');
    });
});

require __DIR__.'/auth.php';

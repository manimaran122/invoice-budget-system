<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductServiceController;
use App\Http\Controllers\Admin\PurchaseInvoiceController;
use App\Http\Controllers\Admin\SalesInvoiceController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::controller(LoginController::class)->group(function () {
    Route::get('/', 'welcome');
    Route::get('/dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/customers/data', [CustomerController::class, 'data'])
            ->name('customers.data');

        Route::resource('customers', CustomerController::class)
            ->except(['show']);

        Route::get('/suppliers/data', [SupplierController::class, 'data'])
            ->name('suppliers.data');

        Route::resource('suppliers', SupplierController::class)
            ->except(['show']);

        Route::get('/product-services/data', [ProductServiceController::class, 'data'])
            ->name('product-services.data');

        Route::resource('product-services', ProductServiceController::class)
            ->except(['show']);

        Route::get('/budgets/data', [BudgetController::class, 'data'])
            ->name('budgets.data');

        Route::resource('budgets', BudgetController::class)
            ->except(['show']);

        Route::get('/purchase-invoices/data', [PurchaseInvoiceController::class, 'data'])
            ->name('purchase-invoices.data');

        Route::resource('purchase-invoices', PurchaseInvoiceController::class);

        Route::get('/sales-invoices/data', [SalesInvoiceController::class, 'data'])
            ->name('sales-invoices.data');

        Route::resource('sales-invoices', SalesInvoiceController::class);
    });

require __DIR__.'/auth.php';

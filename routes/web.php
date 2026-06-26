<?php

use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductServiceController;
use App\Http\Controllers\Admin\PurchaseInvoiceController;
use App\Http\Controllers\Admin\ReportController;
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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });

    Route::controller(CustomerController::class)->prefix('customers')->name('customers.')->group(function () {
        Route::get('/data', 'data')->name('data');
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{customer}/edit', 'edit')->name('edit');
        Route::patch('/{customer}', 'update')->name('update');
        Route::put('/{customer}', 'update')->name('update');
        Route::delete('/{customer}', 'destroy')->name('destroy');
    });

    Route::controller(SupplierController::class)->prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/data', 'data')->name('data');
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{supplier}/edit', 'edit')->name('edit');
        Route::patch('/{supplier}', 'update')->name('update');
        Route::put('/{supplier}', 'update')->name('update');
        Route::delete('/{supplier}', 'destroy')->name('destroy');
    });

    Route::controller(ProductServiceController::class)->prefix('product-services')->name('product-services.')->group(function () {
        Route::get('/data', 'data')->name('data');
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{product_service}/edit', 'edit')->name('edit');
        Route::patch('/{product_service}', 'update')->name('update');
        Route::put('/{product_service}', 'update')->name('update');
        Route::delete('/{product_service}', 'destroy')->name('destroy');
    });

    Route::controller(BudgetController::class)->prefix('budgets')->name('budgets.')->group(function () {
        Route::get('/data', 'data')->name('data');
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{budget}/edit', 'edit')->name('edit');
        Route::patch('/{budget}', 'update')->name('update');
        Route::put('/{budget}', 'update')->name('update');
        Route::delete('/{budget}', 'destroy')->name('destroy');
    });

    Route::controller(ExpenseController::class)->prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/data', 'data')->name('data');
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{expense}/edit', 'edit')->name('edit');
        Route::patch('/{expense}', 'update')->name('update');
        Route::put('/{expense}', 'update')->name('update');
        Route::delete('/{expense}', 'destroy')->name('destroy');
    });

    Route::controller(ReportController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::controller(PurchaseInvoiceController::class)->prefix('purchase-invoices')->name('purchase-invoices.')->group(function () {
        Route::get('/data', 'data')->name('data');
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{purchase_invoice}', 'show')->name('show');
        Route::get('/{purchase_invoice}/edit', 'edit')->name('edit');
        Route::patch('/{purchase_invoice}', 'update')->name('update');
        Route::put('/{purchase_invoice}', 'update')->name('update');
        Route::delete('/{purchase_invoice}', 'destroy')->name('destroy');
    });

    Route::controller(PaymentController::class)->prefix('purchase-invoices')->name('purchase-invoices.')->group(function () {
        Route::post('/{purchase_invoice}/payments', 'storePurchase')->name('payments.store');
    });

    Route::controller(SalesInvoiceController::class)->prefix('sales-invoices')->name('sales-invoices.')->group(function () {
        Route::get('/data', 'data')->name('data');
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{sales_invoice}', 'show')->name('show');
        Route::get('/{sales_invoice}/edit', 'edit')->name('edit');
        Route::patch('/{sales_invoice}', 'update')->name('update');
        Route::put('/{sales_invoice}', 'update')->name('update');
        Route::delete('/{sales_invoice}', 'destroy')->name('destroy');
    });

    Route::controller(PaymentController::class)->prefix('sales-invoices')->name('sales-invoices.')->group(function () {
        Route::post('/{sales_invoice}/payments', 'storeSales')->name('payments.store');
    });

    Route::controller(CurrencyController::class)->prefix('currency-rates')->name('currency.')->group(function () {
        Route::get('/', 'index')->name('index');
    });
});

require __DIR__.'/auth.php';

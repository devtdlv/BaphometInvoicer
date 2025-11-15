<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    // Quotes
    Route::resource('quotes', QuoteController::class);
    Route::post('quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
    Route::post('quotes/{quote}/convert', [QuoteController::class, 'convert'])->name('quotes.convert');
    Route::get('quotes/{quote}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');

    // Clients
    Route::resource('clients', ClientController::class);

    // Payments
    Route::get('invoices/{invoice}/pay', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('invoices/{invoice}/pay', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{gateway}/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    Route::get('payments/{invoice}/success', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('payments/{invoice}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('client', [ReportController::class, 'client'])->name('client');
        Route::get('export', [ReportController::class, 'export'])->name('export');
    });

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Bulk Actions
    Route::post('invoices/bulk', [InvoiceController::class, 'bulkAction'])->name('invoices.bulk');
    Route::get('invoices/{invoice}/attachments/{attachment}', [InvoiceController::class, 'downloadAttachment'])
        ->name('invoices.attachments.download');
});

// Client Portal
Route::prefix('portal')->middleware('auth')->name('portal.')->group(function () {
    Route::get('/', [ClientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('invoices/{invoice}', [ClientPortalController::class, 'invoice'])->name('invoice');
});

require __DIR__.'/auth.php';


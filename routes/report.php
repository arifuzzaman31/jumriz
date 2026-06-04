<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chart\ReportController;

// Analytics, Charts, and Financial Report Routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'permission']], function () {

    Route::controller(ReportController::class)->group(function () {
        
        // Chart Data Endpoints
        Route::get('category-stock/chart', 'getCatStock');
        Route::get('last-month/order/chart', 'getOrderData');
        Route::get('last-month/customer/chart', 'getCustomerData');
        Route::get('sales-amount/chart', 'getSaleAmount');

        // Dashboard Report Views
        Route::get('stock-report', 'stockReport')->name('stock.report');
        Route::get('sales-report', 'salesReport')->name('sales.report');
        Route::get('invoice-report', 'invoiceReport')->name('invoice.report');
        Route::get('transaction', 'showTransection')->name('transection.report');

        // Inventory & Stock Exports
        Route::get('product-report-pdf', 'productListPdf');
        Route::get('product-report-print', 'productListPrint');

        // Sales Reports & Exports
        Route::get('product-sale-report', 'saleList');
        Route::get('product-sales-report-pdf', 'salesListPdf');
        Route::get('product-sales-report-print', 'salesListPrint');

        // Invoice Tracking & Exports
        Route::get('product-invoice-report', 'productInvoiceList');
        Route::get('product-invoice-report-pdf', 'invoiceListPdf');
        Route::get('product-invoice-report-print', 'invoiceListPrint');

        // Payment Gateway Reports
        Route::get('get-payment-method', 'getMethods');
        Route::get('payment-method-wise-amount', 'getMethodAmount');
        Route::get('product-amount-report-pdf', 'amountListPdf');
        Route::get('product-amount-report-print', 'amountListPrint');

        // Global Data Import/Export
        Route::get('export', 'export')->name('export');
        Route::post('import', 'import')->name('import');
        
    });
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\CustomerController;

// All order and customer related routes are here 
Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'permission']], function () {

    // Resource route for standard CRUD operations
    Route::resource('customer', CustomerController::class);

    // Additional customer and order tracking routes
    Route::controller(CustomerController::class)->group(function () {
        Route::get('customer-list', 'getCustomer');
        Route::get('customer/delete/{id}', 'destroy');
        Route::get('customer/{id}/show', 'customerOrder');
        Route::get('customer/orderdetails/{id}/show', 'customerOrderDetails');
        Route::get('customer-pdf-print', 'customerListPrintPdf');
        Route::get('customer-excel', 'customerListExcel');
    });
});
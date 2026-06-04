<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Setting\ShopSettingController;
use App\Http\Controllers\Setting\ShippingAreaController;

// All order and shipping management routes are here 
Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'permission']], function () {

    // Main Order Management Routes
    Route::resource('order', OrderController::class);
    Route::controller(OrderController::class)->group(function () {
        Route::get('order-list', 'orderList');
        Route::get('order/delete/{id}', 'destroy');
        Route::get('order/pdf/{id}', 'pdfLoad');
        Route::get('order/print/{id}', 'printLoad');
        Route::get('all-cities', 'getCity');
        Route::get('order/payment-status/{id}', 'changePaymentStatus');
        Route::get('order/{value}/process-status/{id}', 'changeProcessStatus');
        Route::get('order/delete/single-item/{order_id}/{id}', 'deleteSingleOrder');
        Route::get('order-invoice-send/{id}', 'orderDetails');
        Route::post('send-sms-invoice-to-customer', 'sendMail');
        Route::post('order/item-increment/{id}', 'increamentSingleItem');
        
        // Trial Management within Orders
        Route::post('trial-to-invoice/{id}', 'trialToInvoice');
        Route::get('order/delete/trial-item/{id}', 'deleteTrialItem');
        
        // Area Wise Orders
        Route::get('area-wise-order', 'viewAreaOrder')->name('areawiseorder.index');
    });

    // Shop Settings
    Route::get('shop/address', [ShopSettingController::class, 'getAddress']);

    // Shipping Area Management
    Route::controller(ShippingAreaController::class)->group(function () {
        Route::get('order-area', 'index')->name('order-area.index');
        Route::get('order-area-list', 'getAreaData');
        Route::get('change/shipping/status/{id}', 'changeStatus');
        Route::get('area/delete/{id}', 'destroy');
        Route::post('area/store', 'store');
    });
});

// Public / Frontend Order Tracking Routes
Route::controller(OrderController::class)->group(function () {
    Route::get('order-track', 'orderTrack')->name('order.track');
    Route::post('order-track', 'orderTrackPost');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Offers\CampaignController;
use App\Http\Controllers\Offers\SliderController;
use App\Http\Controllers\Offers\CouponController;
use App\Http\Controllers\Offers\SendCouponController;

// This file contains all offer related routes 

Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'permission']], function () {
    
    // Offers / Campaigns
    Route::resource('offer', CampaignController::class);
    Route::controller(CampaignController::class)->group(function () {
        Route::get('offer-list', 'offerList');
        Route::get('offer/{id}/delete', 'destroy');
        Route::put('offer/{id}/update', 'update');
        Route::get('offer/product-list/search', 'productList');
        Route::get('offer/status/{id}', 'offerStatus');
    });

    // Slider
    Route::resource('slider', SliderController::class);
    Route::controller(SliderController::class)->group(function () {
        Route::get('slider-list', 'sliderList');
        Route::get('slider-status/{id}', 'sliderStatus');
    });
    
    // Coupon Management
    Route::resource('coupon', CouponController::class);
    Route::get('coupon-list', [CouponController::class, 'couponList']);
    
    // Send Coupon
    Route::resource('send-coupon', SendCouponController::class);
    Route::controller(SendCouponController::class)->group(function () {
        Route::get('customer-coupon-list', 'customerCouponList');
        Route::post('get-customer-for-coupon', 'getCustomer');
    });
});

// Public / User Coupon Application
Route::controller(CouponController::class)->group(function () {
    Route::get('user-coupon', 'userCoupon');
    Route::post('apply-coupon', 'applyCoupon');
});
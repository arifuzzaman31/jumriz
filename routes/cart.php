<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Trial\TrialController;

// Cart related routes
Route::controller(CartController::class)->group(function () {
    Route::post('add-to-cart', 'store');
    Route::get('cart-items', 'CartItem');
    Route::get('cart/remove/{id}', 'destroy');
    Route::get('cart/update/{id}/{status}', 'update');
    Route::get('get-shipping', 'shippingAmount');
});

// Trial related routes
Route::controller(TrialController::class)->group(function () {
    Route::post('add-to-trial', 'store');
    Route::get('trial-items', 'TrialItem');
    Route::get('trial/remove/{id}', 'destroy');
    Route::get('trial/update/{id}/{status}', 'update');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::controller(CartController::class)->group(function () {
        Route::get('checkout', 'checkOutPage')->name('checkout.get');
        Route::post('checkout', 'checkoutStore')->name('checkout.store');
    });
});
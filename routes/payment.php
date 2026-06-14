<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\PaypalController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\Payment\RazorpayController;
use App\Http\Controllers\Payment\FlutterWavePaymentController;
use App\Http\Controllers\Setting\PaymentSettingController;
use App\Http\Controllers\Order\OrderController;

// Get available payment methods from database 
Route::get('payment-method-list', [PaymentSettingController::class, 'frontMethodList']);

// Authenticated Payment Initiation Routes
Route::middleware(['auth'])->group(function () {
    Route::get('ssl-commerz/{order_id}', [PaymentController::class, 'sslCommerz'])->name('payment.ssl');
});

// SSLCommerz Webhook / Callback Routes
Route::controller(PaymentController::class)->group(function () {
    Route::post('ssl/success', 'sslCommerzSuccess')->name('ssl.success');
    Route::post('ssl/failed', 'sslCommerzFailed')->name('ssl.failed');
    Route::post('ssl/cancel', 'sslCommerzCancel')->name('ssl.cancel');
});

// PayPal Gateway Routes
Route::controller(PaypalController::class)->group(function () {
    Route::get('paypal/{order_id}', 'payWithpaypal')->name('paypal');
    Route::get('paypal/status/{order_id}', 'getPaymentStatus')->name('paypal.status');
});

// Stripe Gateway Routes
Route::get('stripe/{order_id}', [StripeController::class, 'postPaymentStripe'])->name('addmoney.stripe');

// RazorPay Gateway Routes
Route::controller(RazorpayController::class)->group(function () {
    Route::get('Razorpay/{order_id}', 'Initiate')->name('Razorpay');
    Route::post('payment-complete', 'Complete');
});

// Flutterwave Gateway Routes
Route::controller(FlutterWavePaymentController::class)->group(function () {
    Route::get('Flutterwave/{order_id}', 'getPayment')->name('flutter.payment');
    Route::get('flutter-success', 'successUrl');
    Route::get('flutter-setting', 'flutterSetting');
});

// Post-Order Completion Confirmation
Route::get('order-completed', [OrderController::class, 'orderSuccess'])->name('order.completed');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Setting\CurrencyController;
use App\Http\Controllers\Setting\PaymentSettingController;
use App\Http\Controllers\Setting\SocialCreadentialController;
use App\Http\Controllers\Setting\SeoSettingController;
use App\Http\Controllers\Setting\ShopSettingController;
use App\Http\Controllers\Setting\MessangerController;
use App\Http\Controllers\Setting\ShippingController;
use App\Http\Controllers\Email\EmailController;
use App\Http\Controllers\Setting\PageSettingController;
use App\Http\Controllers\Setting\TrialController;
use App\Http\Controllers\Setting\PwaSettingController;
use App\Http\Controllers\Setting\DeliverySlotSettingController;
use App\Http\Controllers\Setting\TimeSlotSettingController;
use App\Http\Controllers\Auth\CodeVerifyController;

// All configuration and system administration settings routes
Route::group(['prefix' => 'admin/setting', 'middleware' => ['auth:admin', 'permission']], function () {

    // Currency Settings
    Route::resource('currency', CurrencyController::class);
    Route::controller(CurrencyController::class)->group(function () {
        Route::get('currency-list', 'currencyList');
        Route::get('currency/{id}/delete', 'destroy');
        Route::get('current/currency/{id}', 'makeCurrentCurrency');
        Route::post('currency/update/{id}', 'Update');
    });

    // Payment Gateway Settings
    Route::controller(PaymentSettingController::class)->group(function () {
        Route::get('payment-gateway-setting', 'index')->name('gateway.index');
        Route::get('payment-method-list', 'paymentMethodList');
        Route::post('payment-gateway/update/{id}', 'update');
    });

    // Social Login Credentials
    Route::controller(SocialCreadentialController::class)->group(function () {
        Route::get('social/login', 'index')->name('social.index');
        Route::get('social-method-list', 'socialMethodList');
        Route::post('social-creadential/update/{id}', 'update');
    });

    // SEO Configurations
    Route::controller(SeoSettingController::class)->group(function () {
        Route::get('seo', 'index')->name('seo.index');
        Route::get('seo/{id}/edit', 'edit');
        Route::post('seo', 'store');
    });

    // Shop Matrix Configurations
    Route::controller(ShopSettingController::class)->group(function () {
        Route::get('shop', 'index')->name('shop.index');
        Route::get('shop/{id}/edit', 'edit');
        Route::post('shop', 'store');
    });

    // Third-party Scripts & Integrations (Messenger, Google App ID)
    Route::controller(MessangerController::class)->group(function () {
        Route::get('message', 'index')->name('message.analytics');
        Route::get('get-fb-page-data', 'getFbId');
        Route::post('set-fb-page-data', 'setFbId');
        Route::post('change-facebook-id-status', 'setFbStatus');
        Route::get('get-google-app-data', 'getGoogleAppId');
        Route::post('set-google-app-data', 'setGoogleAppId');
        Route::post('change-google-app-status', 'setGoogleStatus');
    });

    // Shipping Matrix Rule Engines
    Route::controller(ShippingController::class)->group(function () {
        Route::get('shipping', 'index')->name('shipping.index');
        Route::get('shipping/discount', 'getShipping');
        Route::post('shipping/discount', 'setShipping');
        Route::post('shipping/status', 'setShippingStatus');
        Route::post('discount/status', 'setDiscountStatus');
    });

    // Email Outbox & Settings
    Route::controller(EmailController::class)->group(function () {
        Route::get('email', 'index')->name('email.index');
        Route::get('get-email', 'getEmails');
        Route::get('get-user-email', 'getUserEmail');
        Route::get('get-subscriber-email', 'getSubscriberEmail');
        Route::post('send/email', 'send');
        Route::get('email/setting', 'showSettingPage')->name('email-setting.index');
        Route::get('mail/data', 'getData')->name('mail/data');
        Route::post('update/mail', 'update')->name('update/mail');
        Route::post('mail/status', 'status')->name('mail/status');
    });

    // Dynamic Pages CMS Content Manager
    Route::controller(PageSettingController::class)->group(function () {
        Route::get('pages', 'getPage')->name('pages.index');
        Route::get('get-pages', 'getPageData')->name('get-pages');
        Route::post('page-store', 'PageStore')->name('page-store');
        Route::post('page/update/{id}', 'update')->name('page/update');
        Route::get('change/publishing/status/{id}', 'changePublish');
        Route::get('page/{id}/delete', 'destroy')->name('page.delete');
    });

    // Product Trial Policies
    Route::controller(TrialController::class)->group(function () {
        Route::get('trial', 'index')->name('trial.index');
        Route::get('trial/{id}/edit', 'edit');
        Route::post('trial', 'update');
    });

    // Progressive Web App (PWA) Engine Configurations
    Route::controller(PwaSettingController::class)->group(function () {
        Route::get('pwa-setting', 'index')->name('pwa.index');
        Route::post('pwa-setting', 'store');
    });

    // Delivery Fleet Date Booking Slots
    Route::controller(DeliverySlotSettingController::class)->group(function () {
        Route::get('date-slot', 'index')->name('slot.index');
        Route::get('get-date-slot', 'getSlot');
        Route::post('date-slot', 'store');
    });

    // Delivery Fleet Time Booking Slots
    Route::resource('time-slot', TimeSlotSettingController::class);
    Route::get('time-slot-list', [TimeSlotSettingController::class, 'index']);

});

// Global Application Activation & Licenses
Route::controller(CodeVerifyController::class)->group(function () {
    Route::get('enter/code', 'enterCode')->name('purchase.code');
    Route::post('post/code', 'verifyCode');
    Route::get('send-verification', 'sendVerification');
});

// Dynamic Public Booking Helper Endpoints
Route::get('time-slot/date/{date}', [TimeSlotSettingController::class, 'getSlotByDate']);
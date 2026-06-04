<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\User\UserController;
use App\Http\Controllers\OTP\OTPController;
use App\Http\Controllers\Admin\AdminController;

// All user account and profile management routes (Authenticated)
Route::middleware(['auth'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('profile', 'index')->name('user.profile');
        Route::get('profile/logout', 'logout')->name('user.logout');
        Route::get('user-dashboard-data', 'dashboardData');
        Route::get('profile/update', 'profileUpdate')->name('user.information');
        Route::get('profile/order', 'order')->name('user.order');
        Route::get('user-order-list', 'getUserOrder')->name('user-order-list');
        Route::get('user/order/{id}/details', 'getUserOrderDetails');
        Route::get('user-order-details-pdf/{id}', 'OrderDetailsPdf');
        
        // Password Management
        Route::get('profile/change-password', 'passwordChange')->name('user.change.password');
        Route::post('profile/change-password', 'storeNewPassword')->name('change.password.post');

        Route::get('authenticate-user', 'authenticateUser');
        Route::post('update-profile', 'storeUpdateProfile');
        Route::get('my-coupon', 'myCoupon')->name('my.coupon');
    });
});

// Administrative/System OTP Toggles
Route::controller(OTPController::class)->group(function () {
    Route::get('otp/enable/{code}', 'enableOtp');
    Route::get('otp/disable/{code}', 'disableOtp');
});

// Guest Authentication, Pass Resets, & OTP Routing
Route::middleware(['guest'])->group(function () {
    
    // OTP Verifications
    Route::controller(OTPController::class)->group(function () {
        Route::post('otp', 'store')->name('send.otp');
        Route::get('otp/submit/{phone}', 'otpForm')->name('otp.form');
        Route::post('otp/submit', 'otpSubmitByUser')->name('submit.otp');
    });

    // Public Password Resets
    Route::controller(UserController::class)->group(function () {
        Route::post('user-password-email-reset-link', 'sendEmailLink')->name('user-password-email-reset-link');
        Route::get('user/reset/{token?}', 'viewUserResetPage');
        Route::post('user/reset/password', 'storeResetPassword')->name('user.confirm.password');
    });

    // Sandbox Environments
    Route::get('dev/test', [AdminController::class, 'tester']);
});
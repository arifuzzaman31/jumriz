<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Wrapping your custom route files with the 'web' middleware group
            Route::middleware('web')->group(function () {
                Route::group([], base_path('routes/product.php'));
                Route::group([], base_path('routes/offers.php'));
                Route::group([], base_path('routes/settings.php'));
                Route::group([], base_path('routes/admin.php'));
                Route::group([], base_path('routes/order.php'));
                Route::group([], base_path('routes/cart.php'));
                Route::group([], base_path('routes/customer.php'));
                Route::group([], base_path('routes/report.php'));
                Route::group([], base_path('routes/user.php'));
                Route::group([], base_path('routes/payment.php'));
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
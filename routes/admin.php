<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AdminLoginController;

// This file contains all admin role and permission related routes

Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'permission']], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('dashboard/summary', [DashboardController::class, 'show']);

    // Role Management
    Route::resource('role', RoleController::class); 
    Route::get('role/delete/{id}', [RoleController::class, 'destroy']);
    Route::post('role/update/{id}', [RoleController::class, 'update']);
    Route::post('permission', [RoleController::class, 'Permission']);
    Route::get('role-list', [RoleController::class, 'RoleList']);
    Route::get('all-role', [RoleController::class, 'allRole']);

    // Admin Management 
    Route::resource('admin', AdminController::class);
    Route::get('admin/delete/{id}', [AdminController::class, 'destroy']);
    Route::get('admin/{id}/edit', [AdminController::class, 'edit']);
    Route::get('admin/status/{id}', [AdminController::class, 'changeStatus']);
    Route::post('admin/update/{id}', [AdminController::class, 'update']);
    Route::get('admin-list', [AdminController::class, 'adminList']);
    Route::get('all-area', [AdminController::class, 'areaList']);

    // Password Management (Authenticated)
    Route::get('password', [AdminController::class, 'getChangePage'])->name('change.password');
    Route::post('password', [AdminController::class, 'saveChangePass'])->name('admin.changepass.submit');
});


// Admin Auth Routes

Route::get('admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');

Route::get('admin/password-change', [AdminLoginController::class, 'showpage'])->name('password-change.request');
Route::post('admin/mail-check', [AdminLoginController::class, 'sendmail'])->name('admin.chkmail.password');
Route::get('admin/reset/{token?}', [AdminLoginController::class, 'viewResetPage']);
Route::post('confirm-password', [AdminLoginController::class, 'storeNewPassword'])->name('admin.confirm.password');

Route::get('admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
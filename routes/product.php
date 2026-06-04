<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\BrandController;
use App\Http\Controllers\Product\SubCategoryController;
use App\Http\Controllers\Product\SubSubCategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ColorController;
use App\Http\Controllers\Product\SizeController;

// This file contains all backend routes for catalog, categories, and products
Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'permission']], function () {
    
    // Category Management
    Route::resource('category', CategoryController::class);
    Route::controller(CategoryController::class)->group(function () {
        Route::post('category/update/{id}', 'update')->name('update.category');
        Route::get('category/delete/{id}', 'destroy')->name('delete.category');
        Route::get('category-list', 'categoryList');
    });

    // Brand Management
    Route::resource('brand', BrandController::class);
    Route::controller(BrandController::class)->group(function () {
        Route::post('brand/update/{id}', 'update')->name('update.brand');
        Route::get('brand/delete/{id}', 'destroy')->name('delete.brand');
        Route::get('brand-list', 'brandList');
    });

    // Sub-Category Management
    Route::resource('sub-category', SubCategoryController::class);
    Route::controller(SubCategoryController::class)->group(function () {
        Route::post('sub-category/update/{id}', 'update')->name('update.subcategory');
        Route::get('sub-category/delete/{id}', 'destroy')->name('delete.subcategory');
        Route::get('sub-category-list', 'subCategoryList');
        // AJAX Endpoints
        Route::get('get-subcategory/{category_id}', 'getSubCategory');
    });

    // Sub-Sub-Category Management
    Route::resource('sub-sub-category', SubSubCategoryController::class);
    Route::controller(SubSubCategoryController::class)->group(function () {
        Route::post('sub-sub-category/update/{id}', 'update')->name('update.subsubcategory');
        Route::get('sub-sub-category/delete/{id}', 'destroy')->name('delete.subsubcategory');
        Route::get('sub-sub-category-list', 'subSubCategoryList');
        // AJAX Endpoints
        Route::get('get-sub-subcategory/{sub_category_id}', 'getSubSubCategory');
        Route::get('get-brand/{sub_sub_category_id}', 'getSubSubCategoryBrand');
    });

    // Product Management
    Route::resource('product', ProductController::class);
    Route::controller(ProductController::class)->group(function () {
        Route::post('product/update/{id}', 'update')->name('product.update');
        Route::get('product/delete/{id}', 'destroy')->name('product.destroy'); // Fixed name typo from subcategory to destroy
        Route::get('product/deactive/{id}', 'deactiveProduct');
        Route::get('all-categories/{edit_time}', 'getAllCategory');
        Route::get('product-list', 'productList');
        Route::get('product/image/{id}/delete', 'deleteImage');
        Route::get('product/hotdeal-status/{id}', 'hotDealStatus');
        Route::get('product/{id}/discount', 'getForDiscount');
        Route::post('set-discount', 'setDiscount');
    });

    // Product Variants (Color & Size)
    Route::resource('product-color', ColorController::class);
    Route::get('color-list', [ColorController::class, 'colorList']);
    Route::get('get-color', [ColorController::class, 'getColor']); // consolidated into ColorController

    Route::resource('product-size', SizeController::class);
    Route::get('size-list', [SizeController::class, 'sizeList']);
    Route::get('get-sizes/{category_id}', [SizeController::class, 'getSizeByCategory']); // consolidated into SizeController

});
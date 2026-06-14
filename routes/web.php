<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\WebController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Offers\CampaignController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [WebController::class, 'index']);
Route::get('page/{slug}', [WebController::class, 'ShowPage']);
Route::get('category-list', [WebController::class, 'categoryList']);
Route::get('home-offers', [WebController::class, 'homeOffers']);
Route::get('hot-deal', [WebController::class, 'hotDeal']);
Route::get('on-sale', [WebController::class, 'onSale']);
Route::get('offers', [CampaignController::class, 'allOffer']);
Route::get('offer/{id}/{name}', [CampaignController::class, 'getOffer'])->name('all-offer.product');

Route::get('related-product/{sub_cat_id}/{id}', [WebController::class, 'relatedDeal']);

Route::post('user/subscribe', [WebController::class, 'subscribe'])->name('user/subscribe');

Route::get('product/category/{id}/{slug}', [WebController::class, 'categoryProduct'])->name('category.product')->where('slug', '.*');

// product details

Route::get('product-by-category-list',[WebController::class, 'productByCategoryList']);
Route::get('view-all-product/category/{category_id}/{slug}',[WebController::class, 'categoryProduct']);

// category product
Route::get('product/category/{id}/{slug?}', [WebController::class, 'categoryProduct'])->name('category.product')->where('slug', '.*');

// sub category product
Route::get('product/sub-category/{id}/{slug?}', [WebController::class, 'subCategory'])->name('subcategory.product')->where('slug', '.*');

// sub category product
Route::get('product/sub-sub-category/{id}/{slug?}', [WebController::class, 'subSubCategory'])->name('subsubcategory.product')->where('slug', '.*');

// product details

Route::get('product/{id}/{slug}', [WebController::class, 'productDetails'])->name('product.details')->where('slug', '.*');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Login with Social media
Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);

Route::get('product-list', [WebController::class, 'productList']);
Route::get('get-search-page', [WebController::class, 'searchPage']);

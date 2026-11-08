<?php

use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Owner\ShopProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicShopController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// ---------- Customers
Route::get('/products', [PublicShopController::class, 'products']);
Route::get('/shops', [PublicShopController::class, 'index']);
Route::get('/shops/{shop}', [PublicShopController::class, 'show']);
Route::get('/shops/{shop}/products/{product}', [PublicShopController::class, 'product']);
Route::get('/settings', [SettingController::class, 'index']);

// ---------- Login ----------
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // ---------- Admin ----------
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('shops', ShopController::class);
        Route::apiResource('products', ProductController::class);
        Route::put('/settings', [SettingController::class, 'update']);
        Route::apiResource('owners', OwnerController::class)->except('show');
    });

    // ---------- Shop owner ----------
    Route::middleware('role:owner')->prefix('owner')->group(function () {
        Route::get('/shop', [ShopProductController::class, 'shop']);
        Route::get('/catalog', [ShopProductController::class, 'catalog']);
        Route::post('/products', [ShopProductController::class, 'createProduct']);
        Route::post('/shop-products', [ShopProductController::class, 'addProduct']);
        Route::put('/shop-products/{product}', [ShopProductController::class, 'updateProduct']);
        Route::delete('/shop-products/{product}', [ShopProductController::class, 'removeProduct']);
    });
});

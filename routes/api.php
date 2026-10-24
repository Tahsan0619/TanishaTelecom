<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/settings', [SiteController::class, 'settings']);
Route::get('/sliders', [SiteController::class, 'sliders']);
Route::get('/pages/{slug}', [SiteController::class, 'page']);

Route::get('/categories', [CatalogController::class, 'categories']);
Route::get('/products', [CatalogController::class, 'products']);
Route::get('/products/{slug}', [CatalogController::class, 'product']);

Route::middleware('throttle:30,1')->group(function () {
    Route::post('/contact', [ContactController::class, 'store']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/track', [OrderController::class, 'track']);
    Route::post('/orders/{orderNumber}/pay', [OrderController::class, 'pay']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/password', [AuthController::class, 'updatePassword']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/my/orders', [OrderController::class, 'myOrders']);
    Route::get('/my/orders/{orderNumber}', [OrderController::class, 'myOrder']);
});

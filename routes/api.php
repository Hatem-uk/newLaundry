<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaundryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\AdminServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ServicePurchaseController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RatingController;


use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::middleware('set.locale')->group(function () {
// Admin routes (authenticated)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function() {
    Route::get('/services', [AdminServiceController::class, 'index']);
    Route::get('/services/pending', [AdminServiceController::class, 'pendingServices']);
    Route::get('/services/{id}', [AdminServiceController::class, 'show']);
    Route::post('/services/{id}/approve', [AdminServiceController::class, 'approve']);
    Route::post('/services/{id}/reject', [AdminServiceController::class, 'reject']);
    Route::get('/services/statistics', [AdminServiceController::class, 'statistics']);
    Route::post('/services/bulk-approve', [AdminServiceController::class, 'bulkApprove']);
    Route::post('/services/bulk-reject', [AdminServiceController::class, 'bulkReject']);
    
    // User management
    Route::get('/users', [AdminServiceController::class, 'getUsers']);
    Route::put('/users/{id}/status', [AdminServiceController::class, 'updateUserStatus']);
    
    // Order management
    Route::get('/orders', [OrderController::class, 'adminOrders']);
    
    // Platform statistics
    Route::get('/statistics', [AdminServiceController::class, 'platformStatistics']);
});

// Auth routes (public)
Route::prefix('auth')->group(function() {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Auth routes (authenticated)
Route::middleware(['auth:sanctum'])->prefix('auth')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/status', [AuthController::class, 'checkStatus']);
    Route::post('/refresh', [AuthController::class, 'refreshToken']);
    Route::get('/profile', [AuthController::class, 'getProfile']);
});

// City routes (public)
Route::prefix('cities')->group(function() {
    Route::get('/', [CityController::class, 'index']);
    Route::get('/regions', [CityController::class, 'regions']);
    Route::get('/with-laundries', [CityController::class, 'withLaundries']);
    Route::get('/with-agents', [CityController::class, 'withAgents']);
    Route::get('/region/{region}', [CityController::class, 'byRegion']);
    Route::get('/{id}', [CityController::class, 'show']);
});



// Package routes (public)
Route::prefix('packages')->group(function() {
    Route::get('/', [PackageController::class, 'index']);
    Route::get('/type/{type}', [PackageController::class, 'byType']);
    Route::get('/{id}', [PackageController::class, 'show']);
});

// Order routes (authenticated)
Route::middleware(['auth:sanctum'])->prefix('orders')->group(function() {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::post('/purchase-service', [OrderController::class, 'purchaseService']);
    Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
    Route::get('/provider/orders', [OrderController::class, 'providerOrders']);
    Route::get('/history/purchases', [OrderController::class, 'purchaseHistory']);
    Route::get('/statistics', [OrderController::class, 'statistics']);
});

// Rating routes (authenticated)
Route::middleware(['auth:sanctum'])->prefix('ratings')->group(function() {
    Route::post('/', [RatingController::class, 'store']);
    Route::put('/{id}', [RatingController::class, 'update']);
    Route::delete('/{id}', [RatingController::class, 'destroy']);
    Route::get('/{id}', [RatingController::class, 'show']);
    Route::get('/laundry/{laundryId}', [RatingController::class, 'getLaundryRatings']);
    Route::get('/customer/me', [RatingController::class, 'getCustomerRatings']);
    Route::get('/stats/{laundryId}', [RatingController::class, 'getRatingStats']);
    Route::get('/search', [RatingController::class, 'search']);
});

// Package purchase routes (authenticated)
Route::middleware(['auth:sanctum'])->prefix('packages')->group(function() {
    Route::post('/purchase', [PackageController::class, 'purchase']);
    Route::post('/gift', [PackageController::class, 'gift']);
});

// Laundry routes (authenticated)
Route::middleware(['auth:sanctum', 'role:laundry'])->prefix('laundry')->group(function() {
    Route::get('/profile', [LaundryController::class, 'profile']);
    Route::put('/profile', [LaundryController::class, 'updateProfile']);
    Route::put('/status', [LaundryController::class, 'updateStatus']);
    Route::get('/statistics', [LaundryController::class, 'statistics']);
    Route::get('/services', [LaundryController::class, 'getServices']);
    Route::get('/nearby-agents', [LaundryController::class, 'getNearbyAgents']);
    Route::get('/agents-by-city/{cityId}', [LaundryController::class, 'getAgentsByCity']);
    
    // Agent supply purchase
    Route::post('/purchase-agent-supply', [ServicePurchaseController::class, 'purchaseAgentSupply']);
    Route::get('/workers', [LaundryController::class, 'indexWorkers']);
    Route::post('/workers', [LaundryController::class, 'addWorker']);
    Route::get('/workers/pending', [LaundryController::class, 'pendingWorkers']);
    Route::post('/workers/{worker}/approve', [LaundryController::class, 'approveWorker']);
});

// Service management routes (authenticated)
Route::middleware(['auth:sanctum', 'role:laundry'])->prefix('services')->group(function() {
    Route::get('/', [ServiceController::class, 'index']);
    Route::post('/', [ServiceController::class, 'store']);
    Route::get('/{id}', [ServiceController::class, 'show']);
    Route::put('/{id}', [ServiceController::class, 'update']);
    Route::delete('/{id}', [ServiceController::class, 'destroy']);
    Route::get('/statistics', [ServiceController::class, 'statistics']);
});

// Customer routes (authenticated)
Route::middleware(['auth:sanctum', 'role:customer'])->prefix('customer')->group(function() {
    Route::get('/profile', [CustomerController::class, 'profile']);
    Route::put('/profile', [CustomerController::class, 'updateProfile']);
    Route::get('/nearby-laundries', [CustomerController::class, 'getNearbyLaundries']);
    Route::get('/nearby-agents', [CustomerController::class, 'getNearbyAgents']);
    Route::get('/favorite-services', [CustomerController::class, 'getFavoriteServices']);
    Route::get('/recent-searches', [CustomerController::class, 'getRecentSearches']);
});

// Service purchase routes (for customers)
Route::middleware(['auth:sanctum', 'role:customer'])->prefix('customer')->group(function() {
    Route::get('/services', [ServicePurchaseController::class, 'availableServices']);
    Route::get('/services-by-location', [ServicePurchaseController::class, 'getServicesByLocation']);
    Route::get('/services/{serviceId}', [ServicePurchaseController::class, 'getServiceDetails']);
    Route::get('/orders', [CustomerController::class, 'getOrders']);
    Route::get('/orders/{id}', [CustomerController::class, 'getOrder']);
});

});
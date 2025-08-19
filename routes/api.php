<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductMediaController;
use App\Http\Controllers\Api\CategoryMediaController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CatalogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public catalog routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/catalog', [CatalogController::class, 'all']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Add your protected routes here
    // Example: Route::apiResource('posts', PostController::class);

    // Profile endpoints
    Route::get('/user', [ProfileController::class, 'me']);
    Route::put('/user', [ProfileController::class, 'update']);
    Route::put('/user/password', [ProfileController::class, 'changePassword']);
    Route::delete('/user', [ProfileController::class, 'destroy']);

    // Admin-only routes
    Route::prefix('admin')->middleware(EnsureUserIsAdmin::class)->group(function () {
        // Users management
        Route::get('/users', [\App\Http\Controllers\Api\Admin\UserAdminController::class, 'index']);
        Route::post('/users/{id}/block', [\App\Http\Controllers\Api\Admin\UserAdminController::class, 'block']);
        Route::post('/users/{id}/unblock', [\App\Http\Controllers\Api\Admin\UserAdminController::class, 'unblock']);

        // Categories CRUD
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Products CRUD
        Route::get('/products', [\App\Http\Controllers\Api\Admin\ProductAdminController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Product media
        Route::post('/products/{id}/image', [ProductMediaController::class, 'upload']);

        // Category media
        Route::post('/categories/{id}/image', [CategoryMediaController::class, 'upload']);

        // Payments
        Route::get('/payments', [\App\Http\Controllers\Api\Admin\PaymentAdminController::class, 'index']);
        Route::get('/payments/{id}', [\App\Http\Controllers\Api\Admin\PaymentAdminController::class, 'show']);
        Route::put('/payments/{id}/status', [\App\Http\Controllers\Api\Admin\PaymentAdminController::class, 'updateStatus']);
        Route::post('/payments/{id}/refund', [\App\Http\Controllers\Api\Admin\PaymentAdminController::class, 'refund']);
    });

    // Also keep product image upload without admin prefix for client compatibility
    Route::post('/products/{id}/image', [ProductMediaController::class, 'upload']);

    // Also allow category image upload without admin prefix
    Route::post('/categories/{id}/image', [CategoryMediaController::class, 'upload']);

    // Cart (server-side)
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{itemId}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{itemId}', [CartController::class, 'removeItem']);
    Route::delete('/cart', [CartController::class, 'clear']);
});

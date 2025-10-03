<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KHQRController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\BakongController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;


// Authentication routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/refresh', [AuthController::class, 'refreshToken']);
    });

    // Your existing protected routes
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/', 'getCategories');
        Route::post('/', 'createCategory');
        Route::get('/{category}', 'getCategory');
        Route::patch('/{category}', 'updateCategory');
        Route::delete('/{category}', 'deleteCategory');
    });

    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('/', 'getProducts');
        Route::post('/', 'createProduct');
        Route::get('/{product}', 'getProduct');
        Route::patch('/{product}', 'updateProduct');
        Route::delete('/{product}', 'deleteProduct');
    });
});

// Public routes (if you want some endpoints to be accessible without auth)
Route::controller(CategoryController::class)->prefix('public/categories')->group(function () {
    Route::get('/', 'getCategories');
    Route::get('/{category}', 'getCategory');
});

Route::controller(ProductController::class)->prefix('public/products')->group(function () {
    Route::get('/', 'getProducts');
    Route::get('/{product}', 'getProduct');
});

// KHQR Payment Routes
Route::prefix('khqr')->group(function () {
    // Basic KHQR generation
    Route::post('/individual', [KHQRController::class, 'generateIndividual']);
    Route::post('/merchant', [KHQRController::class, 'generateMerchant']);
    
    // Generate QR codes with images (for checkout)
    Route::post('/individual-with-image', [KHQRController::class, 'generateIndividualWithImage']);
    Route::post('/generate-qr-image', [KHQRController::class, 'generateQrImage']);

    // QR operations (local)
    Route::post('/decode', [KHQRController::class, 'decode']);
    Route::post('/verify', [KHQRController::class, 'verify']);

    // Payment Detection Routes
    Route::post('/check-payment-status', [KHQRController::class, 'checkPaymentStatus']);
    Route::post('/simulate-payment', [KHQRController::class, 'simulatePayment']); // For testing
});

// Order Management Routes
Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'createOrder']);
    Route::get('/', [OrderController::class, 'listOrders']);
    Route::get('/{id}', [OrderController::class, 'getOrder']);
    Route::post('/{id}/check-payment', [OrderController::class, 'checkPaymentStatus']);
    Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment']);
});




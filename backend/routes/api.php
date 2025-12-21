<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KHQRController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;

// Authentication routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('api.auth')->group(function () {
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

    // Review submission (requires authentication)
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);

    // Cart routes (require authentication)
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart', [CartController::class, 'store']);
    Route::get('cart/{cart}', [CartController::class, 'show']);
    Route::patch('cart/{cart}', [CartController::class, 'update']);
    Route::delete('cart/{cart}', [CartController::class, 'destroy']);
    Route::delete('cart', [CartController::class, 'clear']);
    Route::get('cart-count', [CartController::class, 'count']);

    // Favorites routes (require authentication)
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/', [FavoriteController::class, 'store']);
        Route::post('/toggle', [FavoriteController::class, 'toggle']);
        Route::post('/check', [FavoriteController::class, 'check']);
        Route::delete('/{productId}', [FavoriteController::class, 'destroy']);
    });

});

// Temporarily make favorites public for testing
// Route::prefix('favorites')->group(function () {
//     Route::get('/', [FavoriteController::class, 'index']);
//     Route::post('/', [FavoriteController::class, 'store']);
//     Route::post('/toggle', [FavoriteController::class, 'toggle']);
//     Route::post('/check', [FavoriteController::class, 'check']);
//     Route::delete('/{productId}', [FavoriteController::class, 'destroy']);
// });

Route::controller(ProductController::class)->prefix('products')->group(function () {
    Route::get('/', 'getProducts');
    Route::post('/', 'createProduct');
    Route::get('/{product}', 'getProduct');
    Route::patch('/{product}', 'updateProduct');
    Route::delete('/{product}', 'deleteProduct');
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

// Get reviews for a product (public)
Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);

// KHQR Payment Routes (with CORS)
Route::middleware('api')->prefix('khqr')->group(function () {
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
});

// Order Management Routes (with authentication)
Route::middleware('api.auth')->prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'createOrder']);
    Route::get('/', [OrderController::class, 'listOrders']);
    Route::get('/{id}', [OrderController::class, 'getOrder']);
    Route::post('/{id}/check-payment', [OrderController::class, 'checkPaymentStatus']);
    Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment']);
    Route::patch('/{id}/status', [OrderController::class, 'updateOrderStatus']);
});

// Public discount validation route
Route::post('/discounts/validate', [DiscountController::class, 'validateCode']);

// Admin routes (with authentication)
Route::middleware('api.auth')->prefix('admin')->group(function () {
    // Admin order management
    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::get('/', 'adminListOrders');
        Route::get('/{id}', 'adminGetOrder');
        Route::patch('/{id}/status', 'updateOrderStatus');
    });

    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::post('/', 'createProduct');
        Route::patch('/{product}', 'updateProduct');
        Route::delete('/{product}', 'deleteProduct');
    });
    Route::controller(ReviewController::class)->prefix('reviews')->group(function () {
        Route::get('/', 'adminIndex');
        Route::delete('/{reviewId}', 'adminDestroy');
    });
    Route::patch('/products/{id}/reviews/{reviewId}', [ReviewController::class, 'update']);
    Route::delete('/products/{id}/reviews/{reviewId}', [ReviewController::class, 'destroy']);

    // Discount management routes
    Route::apiResource('discounts', DiscountController::class);
});

// Dashboard routes (admin with CORS)
Route::middleware('api')->prefix('admin/dashboard')->group(function () {
    Route::get('/stats', [DashboardController::class, 'getStats']);
    Route::get('/stock-alerts', [DashboardController::class, 'getStockAlerts']);
    Route::get('/category-distribution', [DashboardController::class, 'getCategoryDistribution']);
});

// User management routes (admin with CORS)
Route::middleware('api')->prefix('admin/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

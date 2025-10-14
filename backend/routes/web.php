<?php

use Illuminate\Support\Facades\Route;

// Basic API info endpoint
Route::get('/', function () {
    return response()->json([
        'message' => 'Motion Cycle E-commerce API',
        'version' => '1.0.0',
        'status' => 'running',
        'docs' => '/api/documentation' // You can add API documentation here
    ]);
});

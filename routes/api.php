<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AgencyController;

// API base routes that don't require auth
Route::get('v1/services', [ServiceController::class, 'index']);
Route::get('v1/services/{service}', [ServiceController::class, 'show']);
Route::post('v1/requests', [RequestController::class, 'store']);
Route::get('v1/quotes/{quote}', [QuoteController::class, 'show']);

// Auth routes
Route::post('v1/login', [AuthController::class, 'login'])->name('api.login');
Route::post('v1/register', [AuthController::class, 'register'])->name('api.register');

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('v1/user', [AuthController::class, 'user']);
    Route::post('v1/logout', [AuthController::class, 'logout']);
    
    // Agency routes
    Route::get('v1/agencies', [AgencyController::class, 'index']);
    Route::get('v1/agencies/{agency}', [AgencyController::class, 'show']);
    
    // Request routes
    Route::get('v1/requests', [RequestController::class, 'index']);
    Route::get('v1/requests/{request}', [RequestController::class, 'show']);
    Route::put('v1/requests/{request}', [RequestController::class, 'update']);
    Route::delete('v1/requests/{request}', [RequestController::class, 'destroy']);
    Route::post('v1/requests/{request}/cancel', [RequestController::class, 'cancel']);
    Route::post('v1/requests/{request}/quotes', [RequestController::class, 'submitQuote']);
    Route::get('v1/requests/{request}/quotes', [RequestController::class, 'getQuotes']);
    
    // Quote routes
    Route::get('v1/quotes', [QuoteController::class, 'index']);
    Route::post('v1/quotes', [QuoteController::class, 'store']);
    Route::put('v1/quotes/{quote}', [QuoteController::class, 'update']);
    Route::delete('v1/quotes/{quote}', [QuoteController::class, 'destroy']);
    Route::patch('v1/quotes/{quote}/accept', [QuoteController::class, 'accept']);
    Route::patch('v1/quotes/{quote}/reject', [QuoteController::class, 'reject']);
});

// Fallback for unauthorized access
Route::fallback(function () {
    return response()->json(['message' => 'API endpoint not found'], 404);
});
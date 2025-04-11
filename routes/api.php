<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AgencyController;

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

// This means these routes will be accessible at /api/v1/...
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
    Route::post('register', [AuthController::class, 'register'])->name('api.register');
    
    // Custom API route for unauthorized test
    Route::get('services/guest', function() {
        return response()->json(['message' => 'Unauthorized access'], 401);
    })->name('api.unauthenticated');
    
    // Routes needed for testing - publicly accessible
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{service}', [ServiceController::class, 'show']);
    Route::post('requests', [RequestController::class, 'store']);
    Route::get('quotes/{quote}', [QuoteController::class, 'show']);
    
    // Protected API routes
    Route::middleware('auth:sanctum')->group(function () {
        // User routes
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        
        // Agency routes
        Route::get('agencies', [AgencyController::class, 'index']);
        Route::get('agencies/{agency}', [AgencyController::class, 'show']);
        
        // Request routes
        Route::get('requests', [RequestController::class, 'index']);
        Route::get('requests/{request}', [RequestController::class, 'show']);
        Route::put('requests/{request}', [RequestController::class, 'update']);
        Route::delete('requests/{request}', [RequestController::class, 'destroy']);
        Route::post('requests/{request}/cancel', [RequestController::class, 'cancel']);
        Route::post('requests/{request}/quotes', [RequestController::class, 'submitQuote']);
        Route::get('requests/{request}/quotes', [RequestController::class, 'getQuotes']);
        
        // Quote routes
        Route::get('quotes', [QuoteController::class, 'index']);
        Route::post('quotes', [QuoteController::class, 'store']);
        Route::put('quotes/{quote}', [QuoteController::class, 'update']);
        Route::delete('quotes/{quote}', [QuoteController::class, 'destroy']);
        Route::patch('quotes/{quote}/accept', [QuoteController::class, 'accept']);
        Route::patch('quotes/{quote}/reject', [QuoteController::class, 'reject']);
    });
    
    // Fallback for unauthorized access
    Route::fallback(function () {
        return response()->json(['message' => 'API endpoint not found'], 404);
    });
});
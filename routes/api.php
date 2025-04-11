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

// واجهة برمجية عامة للتوثيق
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// واجهة برمجية محمية
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // معلومات المستخدم
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    // الوكالات
    Route::get('agencies', [AgencyController::class, 'index']);
    Route::get('agencies/{agency}', [AgencyController::class, 'show']);
    
    // الخدمات
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{service}', [ServiceController::class, 'show']);
    
    // الطلبات
    Route::get('requests', [RequestController::class, 'index']);
    Route::post('requests', [RequestController::class, 'store']);
    Route::get('requests/{request}', [RequestController::class, 'show']);
    
    // عروض الأسعار
    Route::get('quotes', [QuoteController::class, 'index']);
    Route::post('quotes', [QuoteController::class, 'store']);
    Route::get('quotes/{quote}', [QuoteController::class, 'show']);
    Route::patch('quotes/{quote}/accept', [QuoteController::class, 'accept']);
    Route::patch('quotes/{quote}/reject', [QuoteController::class, 'reject']);
});
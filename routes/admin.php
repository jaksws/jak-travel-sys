<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

// Admin routes - explicitly defined with direct middleware class reference
Route::group([
    'middleware' => ['web', 'auth', \App\Http\Middleware\AdminMiddleware::class],
    'prefix' => 'admin',
    'as' => 'admin.'
], function () {
    // Dashboard routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users management
    Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
    
    // Requests management
    Route::get('/requests', [DashboardController::class, 'requests'])->name('requests.index');
    
    // System logs
    Route::get('/system/logs', [DashboardController::class, 'logs'])->name('system.logs');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

// Define admin routes with explicit web middleware
Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users management
    Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
    
    // System logs
    Route::get('/system/logs', [DashboardController::class, 'logs'])->name('system.logs');
});

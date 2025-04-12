<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

// Simplify the middleware chain to avoid any issues
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users management
    Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
    
    // System logs
    Route::get('/system/logs', [DashboardController::class, 'logs'])->name('system.logs');
});

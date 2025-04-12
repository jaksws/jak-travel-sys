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
    Route::get('/users/{id}', [DashboardController::class, 'viewUser'])->name('users.show');
    Route::get('/users/{id}/edit', [DashboardController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [DashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [DashboardController::class, 'deleteUser'])->name('users.destroy');
    Route::patch('/users/{id}/toggle-status', [DashboardController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
    
    // Requests management
    Route::get('/requests', [DashboardController::class, 'requests'])->name('requests.index');
    
    // System logs
    Route::get('/system/logs', [DashboardController::class, 'logs'])->name('system.logs');
    
    // System settings
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
});

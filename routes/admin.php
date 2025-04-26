<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

// ... comments ...

Route::middleware(['auth', 'isAdmin'])->group(function () {
    // ... dashboard and ui routes ...

    // إدارة المستخدمين
    Route::name('admin.')->group(function () {
        Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
        Route::get('/users/{id}', [DashboardController::class, 'viewUser'])->name('users.show');
        Route::get('/users/{id}/edit', [DashboardController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [DashboardController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [DashboardController::class, 'deleteUser'])->name('users.destroy');
        Route::patch('/users/{id}/toggle-status', [DashboardController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');

        // إدارة الطلبات - Move these inside the admin group
        Route::get('/requests', [DashboardController::class, 'requests'])->name('requests.index');
        Route::post('/requests', [DashboardController::class, 'storeRequest'])->name('requests.store');
        // Add missing request routes
        Route::get('/requests/{request}', [DashboardController::class, 'showRequest'])->name('requests.show'); // Assuming showRequest method exists or will be created
        Route::get('/requests/{request}/edit', [DashboardController::class, 'editRequest'])->name('requests.edit'); // Assuming editRequest method exists or will be created
        Route::put('/requests/{request}', [DashboardController::class, 'updateRequest'])->name('requests.update'); // Assuming updateRequest method exists or will be created
        Route::delete('/requests/{request}', [DashboardController::class, 'destroyRequest'])->name('requests.destroy'); // Assuming destroyRequest method exists or will be created
    });

    // إدارة الطلبات - Remove from here
    // Route::get('/requests', [DashboardController::class, 'requests'])->name('requests.index');
    // Route::post('/requests', [DashboardController::class, 'storeRequest'])->name('requests.store');

    // ... system logs and settings routes ...
});
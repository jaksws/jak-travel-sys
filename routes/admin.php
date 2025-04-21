<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

/**
 * في هذا الملف، لا نحتاج لإضافة بادئة 'admin.' للمسارات
 * ولا نحتاج لإضافة بادئة '/admin' للمسارات
 * لأن RouteServiceProvider يقوم بذلك تلقائيًا
 */

// Apply middleware for authentication and admin role
Route::middleware(['auth', 'isAdmin'])->group(function () {
    // مسارات لوحة التحكم
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard'); // تم التعليق لتجنب تكرار اسم المسار

    // إدارة الواجهات - مسارات جديدة لإدارة واجهة المستخدم
    Route::prefix('ui')->name('ui.')->group(function () {
        // إدارة الصفحة الرئيسية
        Route::get('/home', [DashboardController::class, 'homePageManager'])->name('home');
        Route::post('/home', [DashboardController::class, 'updateHomePage'])->name('home.update');

        // إدارة واجهات التطبيق
        Route::get('/interfaces', [DashboardController::class, 'interfacesManager'])->name('interfaces');
        Route::post('/interfaces', [DashboardController::class, 'updateInterfaces'])->name('interfaces.update');

        // تقارير وإحصائيات
        Route::get('/analytics', [DashboardController::class, 'analyticsReports'])->name('analytics');
    });

    // إدارة المستخدمين
    Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
    Route::get('/users/{id}', [DashboardController::class, 'viewUser'])->name('users.show');
    Route::get('/users/{id}/edit', [DashboardController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [DashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [DashboardController::class, 'deleteUser'])->name('users.destroy');
    Route::patch('/users/{id}/toggle-status', [DashboardController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');

    // إدارة الطلبات
    Route::get('/requests', [DashboardController::class, 'requests'])->name('requests.index');

    // سجلات النظام
    Route::get('/system/logs', [DashboardController::class, 'logs'])->name('system.logs');

    // إعدادات النظام
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
});

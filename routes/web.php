<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Agency\DashboardController;
use App\Http\Controllers\Agency\SubagentController;
use App\Http\Controllers\Agency\CustomerController;
use App\Http\Controllers\Agency\ServiceController;
use App\Http\Controllers\Agency\RequestController as AgencyRequestController;
use App\Http\Controllers\Agency\QuoteController as AgencyQuoteController;
use App\Http\Controllers\Agency\TransactionController as AgencyTransactionController;
use App\Http\Controllers\Agency\DocumentController;
use App\Http\Controllers\Agency\SettingsController;
use App\Http\Controllers\Agency\CurrencyController;
use App\Http\Controllers\Agency\ReportController;
use App\Http\Controllers\Subagent\DashboardController as SubagentDashboardController;
use App\Http\Controllers\Subagent\ServiceController as SubagentServiceController;
use App\Http\Controllers\Subagent\RequestController as SubagentRequestController;
use App\Http\Controllers\Subagent\QuoteController as SubagentQuoteController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\Customer\RequestController as CustomerRequestController;
use App\Http\Controllers\Customer\QuoteController as CustomerQuoteController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\DataFixController;
use Illuminate\Support\Facades\File;

// صفحة الترحيب
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// صفحة الخدمات العامة
Route::get('/services', [App\Http\Controllers\HomeController::class, 'publicServices'])->name('public.services');

// تسجيل مسارات المصادقة مرة واحدة فقط
Auth::routes();

// مسارات الملف الشخصي
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// إشعارات المستخدمين (مشتركة لجميع الأنواع)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// مسارات دليل المستخدمين
Route::middleware(['auth'])->group(function () {
    Route::get('/help', [App\Http\Controllers\HelpController::class, 'index'])->name('help.index');
    Route::get('/help/search', [App\Http\Controllers\HelpController::class, 'search'])->name('help.search');
    Route::get('/help/section/{section}', [App\Http\Controllers\HelpController::class, 'showSection'])->name('help.section');
});

// مسارات توجيه المستخدمين الجدد
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding/tips', [App\Http\Controllers\OnboardingController::class, 'tips'])->name('onboarding.tips');
});

// مسارات تفضيلات المستخدم
Route::middleware(['auth'])->group(function () {
    Route::post('/user/preferences', [App\Http\Controllers\UserPreferencesController::class, 'update'])->name('user.preferences.update');
});

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/preferences', [App\Http\Controllers\UserPreferencesController::class, 'index'])->name('preferences');
    Route::post('/preferences', [App\Http\Controllers\UserPreferencesController::class, 'save'])->name('preferences.save');
    Route::get('/preferences/get', [App\Http\Controllers\UserPreferencesController::class, 'getPreferences'])->name('preferences.get');
});

// مسارات الوكيل الأساسي - usando la clase middleware directamente
Route::prefix('agency')->middleware(['auth', \App\Http\Middleware\AgencyMiddleware::class])->name('agency.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // إدارة السبوكلاء
    Route::resource('subagents', SubagentController::class);
    Route::patch('/subagents/{subagent}/toggle-status', [SubagentController::class, 'toggleStatus'])->name('subagents.toggle-status');
    Route::patch('/subagents/{subagent}/update-services', [SubagentController::class, 'updateServices'])->name('subagents.update-services');
    
    // إدارة العملاء
    Route::resource('customers', CustomerController::class);
    Route::patch('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    
    // إدارة الخدمات - Fix duplicate route name issue
    Route::resource('services', ServiceController::class);
    Route::patch('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    
    // إدارة الطلبات
    Route::resource('requests', AgencyRequestController::class);
    Route::patch('/requests/{request}/update-status', [AgencyRequestController::class, 'updateStatus'])->name('requests.update_status');
    Route::post('/requests/{request}/share', [AgencyRequestController::class, 'shareWithSubagents'])->name('requests.share');
    
    // إدارة عروض الأسعار
    Route::resource('quotes', AgencyQuoteController::class);
    Route::get('/quotes', [AgencyQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quote}', [AgencyQuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{quote}/approve', [AgencyQuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('/quotes/{quote}/reject', [AgencyQuoteController::class, 'reject'])->name('quotes.reject');
    
    // إدارة المعاملات المالية
    Route::resource('transactions', AgencyTransactionController::class);
    Route::get('/transactions', function () {
        return view('agency.transactions.index');
    })->name('transactions.index');
    
    // إدارة المستندات
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // معلومات الوكالة
    Route::patch('/info', [DashboardController::class, 'updateAgencyInfo'])->name('update-info');
    
    // مسارات التقارير
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue-by-service', [ReportController::class, 'revenueByService'])->name('reports.revenue-by-service');
    Route::get('/reports/revenue-by-subagent', [ReportController::class, 'revenueBySubagent'])->name('reports.revenue-by-subagent');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    
    // إدارة الإعدادات
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // إدارة العملات
    Route::get('/settings/currencies', [CurrencyController::class, 'index'])->name('settings.currencies');
    Route::post('/settings/currencies', [CurrencyController::class, 'store'])->name('settings.currencies.store');
    Route::put('/settings/currencies/{currency}', [CurrencyController::class, 'update'])->name('settings.currencies.update');
    Route::patch('/settings/currencies/{currency}/toggle', [CurrencyController::class, 'toggleStatus'])->name('settings.currencies.toggle-status');
    Route::patch('/settings/currencies/{currency}/default', [CurrencyController::class, 'setAsDefault'])->name('settings.currencies.set-default');
    Route::delete('/settings/currencies/{currency}', [CurrencyController::class, 'destroy'])->name('settings.currencies.destroy');
    
    // الإشعارات
    Route::get('/notifications', [App\Http\Controllers\Agency\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\Agency\NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Agency\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// مسارات السبوكيل
Route::prefix('subagent')->middleware(['auth', \App\Http\Middleware\SubagentMiddleware::class])->name('subagent.')->group(function () {
    Route::get('/dashboard', [SubagentDashboardController::class, 'index'])->name('dashboard');
    
    // الخدمات المتاحة
    Route::get('/services/create', [SubagentServiceController::class, 'create'])->name('services.create');
    Route::get('/services', [SubagentServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [SubagentServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [SubagentServiceController::class, 'show'])->name('services.show');
    
    // طلبات عروض الأسعار
    Route::get('/requests', [SubagentRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{request}', [SubagentRequestController::class, 'show'])->name('requests.show');
    
    // إدارة عروض الأسعار
    Route::get('/quotes', [SubagentQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create/{request}', [SubagentQuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [SubagentQuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}', [SubagentQuoteController::class, 'show'])->name('quotes.show');
    Route::get('/quotes/{quote}/edit', [SubagentQuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [SubagentQuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [SubagentQuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::delete('/quotes/{quote}/cancel', [SubagentQuoteController::class, 'destroy'])->name('quotes.cancel');
    
    // الإشعارات
    Route::get('/notifications', [App\Http\Controllers\Subagent\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\Subagent\NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Subagent\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// مسارات العميل
Route::prefix('customer')->middleware(['auth', \App\Http\Middleware\CustomerMiddleware::class])->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/requests', [CustomerRequestController::class, 'index'])->name('requests.index');
    Route::get('/quotes', [CustomerQuoteController::class, 'index'])->name('quotes.index');
    
    // الخدمات المتاحة
    Route::get('/services', [CustomerServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [CustomerServiceController::class, 'show'])->name('services.show');
    
    // إدارة الطلبات
    Route::resource('requests', CustomerRequestController::class);
    Route::post('/requests/{request}/cancel', [CustomerRequestController::class, 'cancel'])->name('requests.cancel');
    
    // عروض الأسعار
    Route::get('/quotes', [CustomerQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quote}', [CustomerQuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{quote}/approve', [CustomerQuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('/quotes/{quote}/reject', [CustomerQuoteController::class, 'reject'])->name('quotes.reject');
    
    // الملف الشخصي
    Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    
    // الدعم الفني
    Route::get('/support', [App\Http\Controllers\Customer\SupportController::class, 'index'])->name('support');
    Route::post('/support', [App\Http\Controllers\Customer\SupportController::class, 'submit'])->name('support.submit');
    
    // الإشعارات
    Route::get('/notifications', [App\Http\Controllers\Customer\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\Customer\NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Customer\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// مسارات تستخدم في الاختبارات
Route::post('/requests', [CustomerRequestController::class, 'store'])->name('requests.store');

// مسار تحميل المستندات
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download')->middleware('auth');

// Add authentication middleware for all routes
Route::middleware(['web', 'auth'])->group(function () {
    // Customer routes
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::patch('/requests/{request}', [RequestController::class, 'update'])->name('requests.update');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::get('/requests/{request}', [RequestController::class, 'show'])->name('requests.show');
    
    // Quote routes
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::patch('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::patch('/quotes/{quote}/accept', [QuoteController::class, 'accept'])->name('quotes.accept');
    Route::patch('/quotes/{quote}/reject', [QuoteController::class, 'reject'])->name('quotes.reject');
    
    // Admin routes
    Route::get('/admin/requests', [RequestController::class, 'adminIndex'])->name('admin.requests.index');
    
    // Notifications routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    
    // Test routes for views that don't exist yet
    Route::get('admin/requests', [DataFixController::class, 'getView'])->name('admin.requests.index')->defaults('viewName', 'admin.requests.index');
});

// Define admin routes directly in web.php to ensure they are registered
Route::group([
    'middleware' => ['web', 'auth', \App\Http\Middleware\AdminMiddleware::class],
    'prefix' => 'admin',
    'as' => 'admin.'
], function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // إدارة الطلبات
    Route::get('/requests', [\App\Http\Controllers\Admin\DashboardController::class, 'requests'])->name('requests.index');
    Route::post('/requests', [\App\Http\Controllers\Admin\DashboardController::class, 'storeRequest'])->name('requests.store');
    Route::get('/requests/{request}', [\App\Http\Controllers\Admin\DashboardController::class, 'showRequest'])->name('requests.show');
    Route::get('/requests/{request}/edit', [\App\Http\Controllers\Admin\DashboardController::class, 'editRequest'])->name('requests.edit');
    Route::put('/requests/{request}', [\App\Http\Controllers\Admin\DashboardController::class, 'updateRequest'])->name('requests.update');
    Route::delete('/requests/{request}', [\App\Http\Controllers\Admin\DashboardController::class, 'destroyRequest'])->name('requests.destroy');

    // إدارة المستخدمين (Admin Users Management)
    Route::get('/users', [\App\Http\Controllers\Admin\DashboardController::class, 'users'])->name('users.index');
    Route::get('/users/{id}', [\App\Http\Controllers\Admin\DashboardController::class, 'viewUser'])->name('users.show');
    Route::get('/users/{id}/edit', [\App\Http\Controllers\Admin\DashboardController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [\App\Http\Controllers\Admin\DashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [\App\Http\Controllers\Admin\DashboardController::class, 'deleteUser'])->name('users.destroy');
    Route::patch('/users/{id}/toggle-status', [\App\Http\Controllers\Admin\DashboardController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users', [\App\Http\Controllers\Admin\DashboardController::class, 'storeUser'])->name('users.store');
    
    // System logs
    Route::get('/system/logs', [\App\Http\Controllers\Admin\DashboardController::class, 'logs'])->name('system.logs');
    
    // Admin settings routes
    Route::get('/settings', [\App\Http\Controllers\Admin\DashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\DashboardController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/advanced', [\App\Http\Controllers\Admin\DashboardController::class, 'updateAdvancedSettings'])->name('settings.updateAdvancedSettings');
    
    // Create page route
    Route::post('/create-page', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'pageName' => 'required|string|max:255',
        ]);

        $pageName = strtolower(str_replace(' ', '-', $request->pageName));
        $viewPath = resource_path("views/{$pageName}.blade.php");

        // Check if the page already exists
        if (File::exists($viewPath)) {
            return redirect()->back()->with('error', 'Page already exists.');
        }

        // Create the Blade file
        File::put($viewPath, "@extends('layouts.app')\n\n@section('title', '{$request->pageName}')\n\n@section('content')\n<div class=\"container py-5\">\n    <h1 class=\"mb-4\">{$request->pageName}</h1>\n    <p>This is the {$request->pageName} page. Add your content here.</p>\n</div>\n@endsection");

        // Add the route dynamically
        $routePath = base_path('routes/web.php');
        File::append($routePath, "\nRoute::view('/{$pageName}', '{$pageName}')->name('{$pageName}');");

        // Update footer links in the configuration
        $footerConfigPath = config_path('ui.php');
        $currentConfig = include $footerConfigPath;

        $newLink = [
            'text' => $request->pageName,
            'url' => "/{$pageName}",
        ];

        $currentConfig['footer']['links'][] = $newLink;

        // Save the updated configuration
        $configContent = "<?php\n\nreturn " . var_export($currentConfig, true) . ";\n";
        file_put_contents($footerConfigPath, $configContent);

        return redirect()->back()->with('success', 'Page created successfully.');
    })->name('createPage');
});

// Debug route
Route::get('debug-admin-users', [\App\Http\Controllers\Admin\DashboardController::class, 'users']);

// احذف جميع التعريفات الزائدة لمسار agency.requests.index وأبقي فقط على هذا التعريف:
Route::prefix('agency')->group(function () {
    Route::get('requests', function () {
        $requests = collect();
        $services = [];
        return view('agency.requests.index', ['requests' => $requests, 'services' => $services]);
    })->name('agency.requests.index');
});

// Privacy Policy route
Route::view('/privacy', 'privacy')->name('privacy');

// Terms and Conditions route
Route::view('/terms', 'terms')->name('terms');
Route::view('/roles', 'roles')->name('roles');
Route::view('/الخصوصية', 'الخصوصية')->name('الخصوصية');
Route::view('/اتفاقية-المستخدم', 'اتفاقية-المستخدم')->name('اتفاقية-المستخدم');
Route::view('/القوانين', 'القوانين')->name('القوانين');
Route::view('/t1', 't1')->name('t1');

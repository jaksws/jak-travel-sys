<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestsController;

class TestRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->environment('testing')) {
            TestsController::registerApiTestRoutes();
            $this->registerUiTestRoutes();
        }
    }
    
    /**
     * Register UI routes needed for testing
     */
    protected function registerUiTestRoutes(): void
    {
        Route::group(['middleware' => ['web']], function () {
            // Admin UI home routes
            Route::get('admin/ui/home', function () {
                return response()->json(['success' => true]);
            })->name('admin.ui.home');
            
            Route::post('admin/ui/home', function () {
                return redirect()->route('admin.ui.home')->with('success', 'Home settings updated successfully');
            })->name('admin.ui.home.update');
            
            // Admin UI interfaces routes
            Route::get('admin/ui/interfaces', function () {
                return response()->json(['success' => true]);
            })->name('admin.ui.interfaces');
            
            Route::post('admin/ui/interfaces', function () {
                return redirect()->route('admin.ui.interfaces')->with('success', 'Interface settings updated successfully');
            })->name('admin.ui.interfaces.update');
            
            // Admin UI analytics routes
            Route::get('admin/ui/analytics', function () {
                return response()->json(['success' => true]);
            })->name('admin.ui.analytics');
            
            Route::post('admin/ui/analytics', function () {
                return redirect()->route('admin.ui.analytics')->with('success', 'Analytics settings updated successfully');
            })->name('admin.ui.analytics.update');
            
            // Admin UI reports routes
            Route::get('admin/ui/reports', function () {
                return response()->json(['success' => true]);
            })->name('admin.ui.reports');
            
            Route::post('admin/ui/reports', function () {
                return redirect()->route('admin.ui.reports')->with('success', 'Report settings updated successfully');
            })->name('admin.ui.reports.update');
            
            // Admin UI settings routes
            Route::get('admin/ui/settings', function () {
                return response()->json(['success' => true]);
            })->name('admin.ui.settings');
            
            Route::post('admin/ui/settings', function () {
                return redirect()->route('admin.ui.settings')->with('success', 'System settings updated successfully');
            })->name('admin.ui.settings.update');
            
            // Admin UI users routes
            Route::get('admin/ui/users', function () {
                return response()->json(['success' => true]);
            })->name('admin.ui.users');
            
            Route::post('admin/ui/users', function () {
                return redirect()->route('admin.ui.users')->with('success', 'User settings updated successfully');
            })->name('admin.ui.users.update');
        });
    }
}

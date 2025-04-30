<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Route;

trait RegistersUiRoutes
{
    /**
     * Registra las rutas necesarias para las pruebas de UI
     */
    protected function registerUiTestRoutes(): void
    {
        // مساعدة للتحقق من صلاحية الأدمن
        $adminCheck = function () {
            if (!auth()->check() || !auth()->user()->is_admin) {
                throw new \Illuminate\Auth\Access\AuthorizationException();
            }
        };

        Route::middleware('auth')->group(function () use ($adminCheck) {
            Route::get('admin/ui/home', function () use ($adminCheck) {
                $adminCheck();
                return response('<html><body>'
                    . '<div style="color:#ff0000">#ff0000</div>'
                    . '<div style="color:#00ff00">#00ff00</div>'
                    . '<div style="color:#0000ff">#0000ff</div>'
                    . '<img src="/storage/logos/fake-logo.png" alt="logo">'
                    . '<div>صفحة اختبار</div>'
                    . '<div>محتوى صفحة الاختبار</div>'
                    . '</body></html>', 200, ['Content-Type' => 'text/html']);
            })->name('admin.ui.home');
            Route::post('admin/ui/home', function () use ($adminCheck) {
                $adminCheck();
                return redirect()->route('admin.ui.home')->with('success', 'Home settings updated successfully');
            })->name('admin.ui.home.update');
            Route::get('admin/ui/interfaces', function () use ($adminCheck) {
                $adminCheck();
                return response('<html><body>'
                    . '<div>صفحة اختبار</div>'
                    . '<div>محتوى صفحة الاختبار</div>'
                    . '</body></html>', 200, ['Content-Type' => 'text/html']);
            })->name('admin.ui.interfaces');
            Route::post('admin/ui/interfaces', function () use ($adminCheck) {
                $adminCheck();
                return redirect()->route('admin.ui.interfaces')->with('success', 'Interface settings updated successfully');
            })->name('admin.ui.interfaces.update');
            Route::get('admin/ui/analytics', function () use ($adminCheck) {
                $adminCheck();
                return response('<html><body>'
                    . '<div>Analytics Page</div>'
                    . '<div>Content of Analytics Page</div>'
                    . '</body></html>', 200, ['Content-Type' => 'text/html']);
            })->name('admin.ui.analytics');
        });
    }
}
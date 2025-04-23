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
        // Registrar rutas para la gestión de UI si no existen
        if (!Route::has('admin.ui.home')) {
            Route::get('admin/ui/home', function () {
                // Página HTML ficticia que contiene colores, logotipo y página de prueba
                return response('<html><body>'
                    . '<div style="color:#ff0000">#ff0000</div>'
                    . '<div style="color:#00ff00">#00ff00</div>'
                    . '<div style="color:#0000ff">#0000ff</div>'
                    . '<img src="/storage/logos/fake-logo.png" alt="logo">'
                    . '<div>صفحة اختبار</div>'
                    . '<div>محتوى صفحة الاختبار</div>'
                    . '</body></html>', 200, ['Content-Type' => 'text/html']);
            })->name('admin.ui.home');
        }
        
        if (!Route::has('admin.ui.home.update')) {
            Route::post('admin/ui/home', function () {
                return redirect()->route('admin.ui.home')->with('success', 'Home settings updated successfully');
            })->name('admin.ui.home.update');
        }
        
        if (!Route::has('admin.ui.interfaces')) {
            Route::get('admin/ui/interfaces', function () {
                // Página HTML ficticia que contiene página de prueba y su contenido
                return response('<html><body>'
                    . '<div>صفحة اختبار</div>'
                    . '<div>محتوى صفحة الاختبار</div>'
                    . '</body></html>', 200, ['Content-Type' => 'text/html']);
            })->name('admin.ui.interfaces');
        }
        
        if (!Route::has('admin.ui.interfaces.update')) {
            Route::post('admin/ui/interfaces', function () {
                return redirect()->route('admin.ui.interfaces')->with('success', 'Interface settings updated successfully');
            })->name('admin.ui.interfaces.update');
        }
        
        if (!Route::has('admin.ui.analytics')) {
            Route::get('admin/ui/analytics', function () {
                return response()->json(['success' => true]);
            })->name('admin.ui.analytics');
        }
    }
}
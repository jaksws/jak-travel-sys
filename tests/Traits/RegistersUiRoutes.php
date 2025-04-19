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
                return response()->json(['success' => true]);
            })->name('admin.ui.home');
        }
        
        if (!Route::has('admin.ui.home.update')) {
            Route::post('admin/ui/home', function () {
                return redirect()->route('admin.ui.home')->with('success', 'Home settings updated successfully');
            })->name('admin.ui.home.update');
        }
        
        if (!Route::has('admin.ui.interfaces')) {
            Route::get('admin/ui/interfaces', function () {
                return response()->json(['success' => true]);
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
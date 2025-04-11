<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Check if user has admin role - try different column names that might be used
        if (
            (isset($user->role) && in_array($user->role, ['admin', 'superadmin', 'administrator'])) ||
            (isset($user->user_type) && in_array($user->user_type, ['admin', 'superadmin', 'administrator'])) ||
            (isset($user->type) && in_array($user->type, ['admin', 'superadmin', 'administrator'])) ||
            (isset($user->is_admin) && $user->is_admin) ||
            (isset($user->is_superadmin) && $user->is_superadmin)
        ) {
            return $next($request);
        }
        
        return redirect()->route('home')->with('error', 'لا تملك صلاحية الوصول لهذه الصفحة');
    }
}

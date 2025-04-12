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
        
        // Check user is admin using the correct fields from the database schema
        if (
            ($user->user_type === 'admin') || 
            ($user->role === 'admin') || 
            ($user->is_admin == 1)
        ) {
            return $next($request);
        }
        
        return redirect()->route('home')->with('error', 'لا تملك صلاحية الوصول لهذه الصفحة');
    }
}

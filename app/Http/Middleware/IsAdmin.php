<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Add logging

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            Log::info('Access granted to admin user: ' . Auth::user()->id);
            return $next($request);
        }

        Log::warning('Access denied for user: ' . (Auth::check() ? Auth::user()->id : 'Guest'));
        return response()->json(['message' => 'Forbidden'], 403);
    }
}
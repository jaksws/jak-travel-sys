<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * التحقق من صلاحيات المستخدم كمسؤول
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // التحقق من أن المستخدم مسؤول باستخدام عدة طرق
        if (
            (property_exists($user, 'is_admin') && $user->is_admin) ||
            (method_exists($user, 'isAdmin') && $user->isAdmin()) ||
            (property_exists($user, 'role') && in_array(strtolower($user->role), ['admin', 'superadmin'])) ||
            (property_exists($user, 'user_type') && in_array(strtolower($user->user_type), ['admin', 'superadmin'])) ||
            (property_exists($user, 'type') && in_array(strtolower($user->type), ['admin', 'superadmin'])) ||
            (property_exists($user, 'is_superadmin') && $user->is_superadmin)
        ) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
    }
}

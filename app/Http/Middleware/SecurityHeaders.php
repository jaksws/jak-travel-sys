<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * تطبيق رؤوس حماية على جميع الاستجابات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // إضافة رؤوس أمان أساسية
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'same-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=()');

        // في بيئة الإنتاج، نضيف المزيد من رؤوس الحماية
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            
            // سياسة أمان المحتوى - يمكن تخصيصها حسب احتياجات التطبيق
            $cspDirectives = [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",
                "img-src 'self' data:",
                "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
                "connect-src 'self'",
            ];
            
            $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));
        }

        return $response;
    }
}

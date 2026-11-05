<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') or middleware('role:driver')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
        }

        // Check if driver account is suspended/inactive
        if ($role === 'driver') {
            $driver = $request->user()->driver;
            if ($driver && $driver->status === 'inactive') {
                abort(403, 'تم إيقاف حساب السائق من قبل الإدارة.');
            }
        }

        return $next($request);
    }
}

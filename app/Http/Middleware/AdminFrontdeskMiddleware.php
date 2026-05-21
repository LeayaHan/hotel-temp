<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminFrontdeskMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'manager', 'front_desk'])) {
            abort(403, 'Access denied. Housekeeping staff cannot manage guests.');
        }

        return $next($request);
    }
}
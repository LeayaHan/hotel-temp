<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! in_array(auth()->user()->role, ['admin', 'staff', 'front_desk'])) {
            abort(403, 'Access denied. Staff and Admins only.');
        }

        return $next($request);
    }
}
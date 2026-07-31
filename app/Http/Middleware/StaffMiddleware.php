<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'waiter', 'cook'])) {
            abort(403, 'Unauthorized access. Staff credentials required.');
        }

        return $next($request);
    }
}

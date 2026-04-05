<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login')->with('error', 'Please log in to access this area.');
        }

        // Both admin and staff can access staff routes
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            abort(403, 'Access denied. Staff privileges required.');
        }

        return $next($request);
    }
}

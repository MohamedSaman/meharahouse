<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login')->with('error', 'Please log in to access this area.');
        }

        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}

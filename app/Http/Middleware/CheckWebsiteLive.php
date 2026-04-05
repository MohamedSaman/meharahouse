<?php

// app/Http/Middleware/CheckWebsiteLive.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckWebsiteLive
{
    public function handle(Request $request, Closure $next)
    {
        $isLive = Setting::get('website_live', '1');

        if ($isLive !== '1' && $isLive !== true) {
            // Allow admin and staff users through even when the site is in maintenance mode
            if (auth()->check() && in_array(auth()->user()->role, ['admin', 'staff'])) {
                return $next($request);
            }

            // Serve the maintenance page with HTTP 503 to signal search engines / uptime monitors
            $title   = Setting::get('maintenance_title', 'Site Under Maintenance');
            $message = Setting::get('maintenance_message', 'We are performing maintenance. Back shortly!');
            $whatsapp = Setting::get('site_whatsapp', '');

            return response()->view('maintenance', compact('title', 'message', 'whatsapp'), 503);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Services\AutomationRegistry;
use Closure;
use Illuminate\Http\Request;

/**
 * Blocks routes of disabled modules: 404 for guests/API-style, friendly
 * redirect with message for admin web requests.
 */
class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $key)
    {
        if (! AutomationRegistry::isEnabled($key)) {
            if ($request->expectsJson()) {
                abort(404);
            }

            return redirect()->route('admin.automation.index')
                ->withErrors(['module' => 'এই automation এখন বন্ধ আছে (Off)।']);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Addons\AddonManager;
use Closure;
use Illuminate\Http\Request;

/**
 * Every route an add-on registers must carry this middleware. Typing an
 * add-on URL while it is inactive (or not installed) returns a 404 instead
 * of exposing a half-working page.
 */
class EnsureAddonActive
{
    public function handle(Request $request, Closure $next, string $slug)
    {
        if (! app(AddonManager::class)->isActive($slug)) {
            abort(404);
        }

        return $next($request);
    }
}

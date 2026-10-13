<?php

namespace App\Http\Middleware;

use App\Services\Tracking\AttributionService;
use Closure;
use Illuminate\Http\Request;

class TrackingAttributionMiddleware
{
    protected AttributionService $attributionService;

    public function __construct(AttributionService $attributionService)
    {
        $this->attributionService = $attributionService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Capture attribution data
        $this->attributionService->captureAttribution();

        return $next($request);
    }
}

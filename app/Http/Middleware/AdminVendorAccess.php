<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminVendorAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role === 'vendor') {
            if (!$user->is_vendor_approved) {
                Auth::logout();
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Vendor account pending approval.'], 403);
                }
                session()->flash('error', 'Vendor account pending approval.');
                return redirect()->route('login');
            }
            return $next($request);
        }

        // Customer or others
        abort(403, 'Access Denied');
    }
}

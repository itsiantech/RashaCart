<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allow only authenticated customers (role=customer).
 * Admins/vendors keep using their own dashboards.
 */
class EnsureCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || $user->role !== 'customer') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Please login as a customer.', 'login_required' => true], 401);
            }
            // Store intended URL so the modal can send them back after login.
            session(['url.intended' => $request->fullUrl()]);
            return redirect('/')->with('customer_login_required', true);
        }
        return $next($request);
    }
}

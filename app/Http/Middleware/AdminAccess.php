<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $user = Auth::user();

        if (!$user || $user->role !== 'admin') {

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        // if (!$user || ! $user->hasAnyRole(['admin', 'super-admin'])) {
        //     // যদি user না থাকে বা role match না করে
        //     if ($request->expectsJson()) {
        //         return response()->json(['message' => 'Access Denied'], 403);
        //     }

            session()->flash('error', 'Access Denied');
            return redirect(route('home'));
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Check if the authenticated user has the required role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Step 1: Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Step 2: Check if user has the required role
        if (Auth::user()->role !== $role) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Step 3: Allow the request to continue
        return $next($request);
    }
}
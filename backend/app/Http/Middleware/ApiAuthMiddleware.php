<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle API authentication using Laravel Sanctum tokens
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Step 1: Get the bearer token from the request
        $token = $request->bearerToken();

        // Step 2: Check if token is provided
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Step 3: Find the access token in the database
        $accessToken = PersonalAccessToken::findToken($token);

        // Step 4: Check if token exists and has a valid user
        if (!$accessToken || !$accessToken->tokenable) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Step 5: Check if token has expired
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json(['message' => 'Token expired.'], 401);
        }

        // Step 6: Log in the user associated with the token
        Auth::login($accessToken->tokenable);

        // Step 7: Continue to the next middleware/request
        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        // Step 1: Validate the input data
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Step 2: Create the user in the database
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // Step 3: Generate an authentication token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Step 4: Return success response with user and token
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    /**
     * Login user and return token.
     */
    public function login(Request $request)
    {
        // Step 1: Validate the input data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Step 2: Find the user by email
        $user = User::where('email', $request->email)->first();

        // Step 3: Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Step 4: Generate an authentication token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Step 5: Return success response with user and token
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'role' => $user->role,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        // Step 1: Delete the current access token
        $request->user()->currentAccessToken()->delete();

        // Step 2: Return success response
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user details.
     */
    public function user(Request $request)
    {
        // Step 1: Return the authenticated user data
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * Refresh the authentication token.
     */
    public function refreshToken(Request $request)
    {
        // Step 1: Get the authenticated user
        $user = $request->user();

        // Step 2: Delete the current access token
        $user->currentAccessToken()->delete();

        // Step 3: Generate a new authentication token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Step 4: Return success response with new token
        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Get all favorites for the authenticated user.
     */
    public function index()
    {
        // Step 1: Get the authenticated user
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Step 2: Get user's favorites with product details
        $favorites = Favorite::where('user_id', $user->id)->with('product')->get();

        // Step 3: Return the favorites list
        return response()->json(['success' => true, 'data' => $favorites]);
    }

    /**
     * Add a product to favorites.
     */
    public function store(Request $request)
    {
        // Step 1: Validate the input
        $request->validate(['product_id' => 'required|exists:products,id']);

        // Step 2: Get the authenticated user
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Step 3: Check if already favorited
        $exists = Favorite::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Product is already in favorites'], 409);
        }

        // Step 4: Create the favorite
        $favorite = Favorite::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
        ]);

        $favorite->load('product');

        // Step 5: Return success response
        return response()->json([
            'success' => true,
            'data' => $favorite,
            'message' => 'Product added to favorites'
        ], 201);
    }

    /**
     * Check if a product is favorited by the user.
     */
    public function check(Request $request)
    {
        // Step 1: Validate the input
        $request->validate(['product_id' => 'required|exists:products,id']);

        // Step 2: Get the authenticated user
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Step 3: Check if favorited
        $isFavorited = Favorite::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->exists();

        // Step 4: Return the status
        return response()->json(['success' => true, 'is_favorited' => $isFavorited]);
    }

    /**
     * Toggle favorite status for a product.
     */
    public function toggle(Request $request)
    {
        // Step 1: Validate the input
        $request->validate(['product_id' => 'required|exists:products,id']);

        // Step 2: Get the authenticated user
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Step 3: Find existing favorite
        $favorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($favorite) {
            // Step 4a: If exists, remove it
            $favorite->delete();
            return response()->json([
                'success' => true,
                'is_favorited' => false,
                'message' => 'Product removed from favorites'
            ]);
        } else {
            // Step 4b: If not, add it
            $favorite = Favorite::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
            ]);
            $favorite->load('product');
            return response()->json([
                'success' => true,
                'is_favorited' => true,
                'data' => $favorite,
                'message' => 'Product added to favorites'
            ], 201);
        }
    }

    /**
     * Remove a product from favorites.
     */
    public function destroy($productId)
    {
        // Step 1: Get the authenticated user
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Step 2: Find the favorite
        $favorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$favorite) {
            return response()->json(['success' => false, 'message' => 'Favorite not found'], 404);
        }

        // Step 3: Delete the favorite
        $favorite->delete();

        // Step 4: Return success response
        return response()->json(['success' => true, 'message' => 'Product removed from favorites']);
    }
}

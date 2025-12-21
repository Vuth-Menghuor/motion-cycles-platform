<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get the current user ID (authenticated user or default to 1)
     */
    private function getUserId()
    {
        return Auth::id() ?? 1;
    }

    /**
     * Get all items in the user's cart
     */
    public function index()
    {
        // Step 1: Get cart items for current user with product and category details
        $cartItems = Cart::where('user_id', $this->getUserId())
            ->with(['product.category'])
            ->get();

        // Step 2: Return cart items
        return response()->json($cartItems);
    }

    /**
     * Add a product to the cart
     */
    public function store(Request $request)
    {
        // Step 1: Validate input data
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Step 2: Find the product
        $product = Product::findOrFail($request->product_id);

        // Step 3: Check if enough quantity is available
        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'Insufficient product quantity'], 422);
        }

        // Step 4: Add or update cart item
        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => $this->getUserId(),
                'product_id' => $request->product_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        // Step 5: Return the cart item with product details
        return response()->json($cartItem->load(['product.category']), 201);
    }

    /**
     * Update the quantity of a cart item
     */
    public function update(Request $request, Cart $cart)
    {
        // Step 1: Check if cart item belongs to current user
        if ($cart->user_id !== $this->getUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Step 2: Validate input data
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Step 3: Check if enough quantity is available
        $product = $cart->product;
        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'Insufficient product quantity'], 422);
        }

        // Step 4: Update cart item quantity
        $cart->update(['quantity' => $request->quantity]);

        // Step 5: Return updated cart item
        return response()->json($cart->load(['product.category']));
    }

    /**
     * Remove an item from the cart
     */
    public function destroy(Cart $cart)
    {
        // Step 1: Check if cart item belongs to current user
        if ($cart->user_id !== $this->getUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Step 2: Delete the cart item
        $cart->delete();

        // Step 3: Return success message
        return response()->json(['message' => 'Item removed from cart']);
    }

    /**
     * Clear all items from the cart
     */
    public function clear()
    {
        // Step 1: Delete all cart items for current user
        Cart::where('user_id', $this->getUserId())->delete();

        // Step 2: Return success message
        return response()->json(['message' => 'Cart cleared']);
    }

    /**
     * Get the total number of items in the cart
     */
    public function count()
    {
        // Step 1: Sum up all quantities in user's cart
        $count = Cart::where('user_id', $this->getUserId())->sum('quantity');

        // Step 2: Return the count
        return response()->json(['count' => $count]);
    }

    /**
     * Get a specific cart item
     */
    public function show(Cart $cart)
    {
        // Step 1: Check if cart item belongs to current user
        if ($cart->user_id !== $this->getUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Step 2: Return cart item with product details
        return response()->json($cart->load(['product.category']));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    // Get user's cart items
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('product')
            ->get();

        return response()->json($cartItems);
    }

    // Add item to cart
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::findOrFail($request->product_id);

        // Check if product has enough quantity
        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'Insufficient product quantity'], 422);
        }

        // Create or update cart item
        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        return response()->json($cartItem->load('product'), 201);
    }

    // Update cart item quantity
    public function update(Request $request, Cart $cart)
    {
        // Ensure user owns this cart item
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = $cart->product;

        // Check if product has enough quantity
        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'Insufficient product quantity'], 422);
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json($cart->load('product'));
    }

    // Remove item from cart
    public function destroy(Cart $cart)
    {
        // Ensure user owns this cart item
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cart->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    // Clear entire cart
    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return response()->json(['message' => 'Cart cleared']);
    }

    // Get cart count
    public function count()
    {
        $count = Cart::where('user_id', Auth::id())->sum('quantity');

        return response()->json(['count' => $count]);
    }

    // Show specific cart item (required for apiResource)
    public function show(Cart $cart)
    {
        // Ensure user owns this cart item
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($cart->load('product'));
    }
}

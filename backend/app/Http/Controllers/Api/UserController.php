<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users for admin management
     */
    public function index(Request $request)
    {
        try {
            // Step 1: Start building the query
            $query = User::query();

            // Step 2: Apply search filter if provided
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            }

            // Step 3: Get users ordered by creation date
            $users = $query->orderBy('created_at', 'desc')->get();

            // Step 4: Transform users for frontend
            $transformedUsers = $users->map(function ($user) {
                // Step 5: Calculate total orders for each user
                $ordersByUserId = $user->orders()->count();
                $ordersByName = \App\Models\Order::where('customer_name', $user->name)->count();
                $totalOrders = max($ordersByUserId, $ordersByName);

                // Step 6: Return transformed user data
                return [
                    'id' => $user->id,
                    'customer_id' => 'CUST-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'phone' => $user->phone ?? 'N/A',
                    'registration_date' => $user->created_at->toISOString(),
                    'total_orders' => $totalOrders,
                    'status' => 'active',
                    'role' => $user->role ?? 'user',
                ];
            });

            // Step 7: Return success response
            return response()->json([
                'success' => true,
                'data' => $transformedUsers,
                'message' => 'Users retrieved successfully'
            ]);

        } catch (\Exception $e) {
            // Step 8: Handle errors
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific user with their order details
     */
    public function show($id)
    {
        try {
            // Step 1: Find the user
            $user = User::findOrFail($id);

            // Step 2: Get user's orders (both by user_id and customer_name)
            $orders = \App\Models\Order::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('customer_name', $user->name);
            })
            ->with(['user', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($order) {
                // Step 3: Transform order items with product details
                $transformedItems = collect($order->items)->map(function($item) {
                    // Step 4: Try to get product details from database
                    $product = null;
                    if (isset($item['id'])) {
                        $product = \App\Models\Product::with('category')->find($item['id']);
                    }

                    // Step 5: Return transformed item data
                    return [
                        'id' => $item['id'] ?? null,
                        'name' => $item['name'] ?? 'Unknown Product',
                        'price' => $item['price'] ?? 0,
                        'quantity' => $item['quantity'] ?? 1,
                        'image' => $product ? $product->image : 'http://localhost:8000/storage/images/default-product.png',
                        'category' => $product ? ($product->category ? $product->category->name : 'Unknown') : 'Unknown',
                        'brand' => $product ? $product->brand : 'N/A',
                        'color' => $item['color'] ?? 'N/A',
                        'discount' => $product ? $product->discount : null,
                    ];
                });

                // Step 6: Return transformed order data
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'created_at' => $order->created_at->toISOString(),
                    'order_status' => $order->order_status,
                    'payment_status' => $order->payment_status,
                    'total_amount' => $order->total_amount,
                    'subtotal' => $order->subtotal,
                    'discount_amount' => $order->discount_amount,
                    'shipping_amount' => $order->shipping_amount,
                    'customer_phone' => $order->customer_phone,
                    'items_count' => count($order->items),
                    'items' => $transformedItems,
                ];
            });

            // Step 7: Calculate total orders for user
            $totalOrders = max(
                $user->orders()->count(),
                \App\Models\Order::where('customer_name', $user->name)->count()
            );

            // Step 8: Return user data with orders
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'customer_id' => 'CUST-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'phone' => $user->phone ?? 'N/A',
                    'registration_date' => $user->created_at->toISOString(),
                    'total_orders' => $totalOrders,
                    'status' => 'active',
                    'role' => $user->role ?? 'user',
                    'orders' => $orders,
                ],
                'message' => 'User retrieved successfully'
            ]);

        } catch (\Exception $e) {
            // Step 9: Handle errors
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {
        try {
            // Step 1: Find the user
            $user = User::findOrFail($id);

            // Step 2: Delete the user
            $user->delete();

            // Step 3: Return success response
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            // Step 4: Handle errors
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
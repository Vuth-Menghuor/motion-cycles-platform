<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Services\BakongApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Create a new order with customer details and items
     */
    public function createOrder(Request $request)
    {
        try {
            // Step 1: Validate all input data
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'shipping_address' => 'nullable|array',
                'shipping_address.full_name' => 'nullable|string|max:255',
                'shipping_address.street_address' => 'nullable|string|max:255',
                'shipping_address.city' => 'nullable|string|max:255',
                'shipping_address.state' => 'nullable|string|max:255',
                'shipping_address.postal_code' => 'nullable|string|max:20',
                'shipping_address.country' => 'nullable|string|max:255',
                'shipping_address.phone' => 'nullable|string|max:20',
                'items' => 'required|array|min:1',
                'items.*.id' => 'required',
                'items.*.name' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.image' => 'nullable|string',
                'items.*.description' => 'nullable|string',
                'subtotal' => 'required|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'shipping_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'currency' => 'nullable|string|in:USD,KHR',
                'promo_code' => 'nullable|string|max:50',
                'payment_method' => 'required|string',
                'bakong_account' => 'required_if:payment_method,Bakong KHQR|string',
                'account_name' => 'required_if:payment_method,Bakong KHQR|string',
            ]);

            // Step 2: Start database transaction
            DB::beginTransaction();

            // Step 3: Create the order record
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'invoice_number' => Order::generateInvoiceNumber(),
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'items' => $validated['items'],
                'subtotal' => $validated['subtotal'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'shipping_amount' => $validated['shipping_amount'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'currency' => $validated['currency'] ?? 'USD',
                'promo_code' => $validated['promo_code'] ?? null,
                'order_status' => 'pending',
            ]);

            // Step 4: Create the payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'amount' => $validated['total_amount'],
                'currency' => $validated['currency'] ?? 'USD',
            ]);

            // Step 4: Prepare basic response
            $response = [
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'invoice_number' => $order->invoice_number,
                'total_amount' => $order->total_amount,
                'currency' => $order->currency,
            ];

            // Step 5: Handle KHQR payment if selected
            if ($validated['payment_method'] === 'Bakong KHQR') {
                $khqrResult = BakongApiService::generateIndividual(
                    $validated['bakong_account'],
                    $validated['account_name'],
                    $validated['total_amount'],
                    $validated['currency'] ?? 'USD',
                    true
                );

                if ($khqrResult['success']) {
                    // Step 6: Update payment with QR data
                    $payment->update([
                        'qr_code_string' => $khqrResult['qr_string'],
                        'qr_md5_hash' => $khqrResult['md5'] ?? null,
                        'payment_status' => 'pending',
                    ]);

                    // Step 7: Add QR data to response
                    $response['qr_data'] = [
                        'qr_string' => $khqrResult['qr_string'],
                        'md5' => $khqrResult['md5'] ?? null,
                        'tracking_enabled' => isset($khqrResult['md5']),
                        'bakong_account' => $khqrResult['bakong_account'],
                        'account_name' => $khqrResult['account_name'],
                    ];

                    Log::info('Order created with KHQR', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'md5' => $khqrResult['md5'] ?? null,
                    ]);
                } else {
                    throw new \Exception('Failed to generate KHQR: ' . ($khqrResult['message'] ?? 'Unknown error'));
                }
            }

            // Step 8: Commit the transaction
            DB::commit();

            // Step 9: Handle discount usage tracking
            if (!empty($validated['promo_code']) && $validated['discount_amount'] > 0) {
                try {
                    $discount = Discount::where('code', $validated['promo_code'])->first();
                    if ($discount) {
                        $discount->increment('used_count');
                        Log::info('Discount usage incremented', [
                            'discount_id' => $discount->id,
                            'discount_code' => $discount->code,
                            'order_id' => $order->id,
                            'new_used_count' => $discount->used_count,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to increment discount usage count', [
                        'discount_code' => $validated['promo_code'],
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Step 10: Return success response
            return response()->json($response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check the payment status of an order
     */
    public function checkPaymentStatus(Request $request, $id)
    {
        try {
            // Step 1: Find the order
            $order = Order::findOrFail($id);

            // Step 2: Check if payment is already completed
            if ($order->isPaymentCompleted()) {
                return response()->json([
                    'success' => true,
                    'payment_status' => 'completed',
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'invoice_number' => $order->invoice_number,
                        'payment_completed_at' => $order->payment_completed_at,
                    ],
                ]);
            }

            // Step 3: Check KHQR payment status if applicable
            if ($order->qr_md5_hash && $order->payment_method === 'Bakong KHQR') {
                $paymentResult = BakongApiService::checkPaymentStatus($order->qr_md5_hash);

                if ($paymentResult['success'] && $paymentResult['payment_status'] === 'completed') {
                    // Step 4: Mark payment as completed
                    $order->markAsPaymentCompleted($paymentResult['transaction_data'] ?? []);

                    Log::info('Payment confirmed for order', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'md5' => $order->qr_md5_hash,
                    ]);

                    return response()->json([
                        'success' => true,
                        'payment_status' => 'completed',
                        'order' => [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'invoice_number' => $order->invoice_number,
                            'payment_completed_at' => $order->payment_completed_at,
                        ],
                    ]);
                }
            }

            // Step 5: Return current payment status
            return response()->json([
                'success' => true,
                'payment_status' => $order->payment_status,
                'message' => 'Payment not completed yet',
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check failed', [
                'error' => $e->getMessage(),
                'order_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manually confirm payment for an order
     */
    public function confirmPayment(Request $request, $id)
    {
        try {
            // Step 1: Validate input
            $validated = $request->validate([
                'notes' => 'nullable|string|max:500',
            ]);

            // Step 2: Find the order
            $order = Order::findOrFail($id);

            // Step 3: Check if already confirmed
            if ($order->isPaymentCompleted()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment already confirmed',
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'invoice_number' => $order->invoice_number,
                    ],
                ]);
            }

            // Step 4: Mark payment as completed
            $order->markAsPaymentCompleted([
                'confirmation_type' => 'manual',
                'confirmed_by' => Auth::id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            Log::info('Payment manually confirmed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'confirmed_by' => Auth::id(),
            ]);

            // Step 5: Return success response
            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'invoice_number' => $order->invoice_number,
                    'payment_completed_at' => $order->payment_completed_at,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Manual payment confirmation failed', [
                'error' => $e->getMessage(),
                'order_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get details of a specific order
     */
    public function getOrder(Request $request, $id)
    {
        try {
            // Step 1: Find the order
            $order = Order::findOrFail($id);

            // Step 2: Check user permissions
            if (Auth::check() && Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this order',
                ], 403);
            }

            // Step 3: Enrich items with latest product data
            $enrichedItems = $this->enrichOrderItemsWithProductData($order->items);

            // Step 4: Return order details
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'invoice_number' => $order->invoice_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'customer_email' => $order->customer_email,
                    'shipping_address' => $order->shipping_address,
                    'items' => $enrichedItems,
                    'subtotal' => $order->subtotal,
                    'discount_amount' => $order->discount_amount,
                    'shipping_amount' => $order->shipping_amount,
                    'total_amount' => $order->total_amount,
                    'currency' => $order->currency,
                    'formatted_total' => $order->formatted_total,
                    'promo_code' => $order->promo_code,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'payment_completed_at' => $order->payment_completed_at,
                    'confirmed_at' => $order->confirmed_at,
                    'processing_at' => $order->processing_at,
                    'shipped_at' => $order->shipped_at,
                    'delivered_at' => $order->delivered_at,
                    'tracking_number' => $order->tracking_number,
                    'notes' => $order->notes,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
    }

    /**
     * Update the status of an order
     */
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            // Step 1: Validate input
            $validated = $request->validate([
                'order_status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
                'payment_status' => 'nullable|in:pending,processing,completed,failed,refunded',
                'notes' => 'nullable|string|max:500',
                'tracking_number' => 'nullable|string|max:100',
            ]);

            // Step 2: Find the order
            $order = Order::findOrFail($id);

            // Step 3: Check user permissions
            if (Auth::check() && Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this order',
                ], 403);
            }

            // Step 4: Prepare update data
            $updateData = [
                'order_status' => $validated['order_status'],
            ];

            if (isset($validated['payment_status'])) {
                // Update payment status on the payment record
                if ($order->payment) {
                    $order->payment->update(['payment_status' => $validated['payment_status']]);
                }
            }

            if ($validated['order_status'] === 'shipped' && isset($validated['tracking_number'])) {
                $updateData['tracking_number'] = $validated['tracking_number'];
            }

            if (isset($validated['notes'])) {
                $updateData['notes'] = $validated['notes'];
            }

            // Step 5: Set timestamps based on status
            switch ($validated['order_status']) {
                case 'confirmed':
                    $updateData['confirmed_at'] = now();
                    break;
                case 'processing':
                    $updateData['processing_at'] = now();
                    break;
                case 'shipped':
                    $updateData['shipped_at'] = now();
                    break;
                case 'delivered':
                    $updateData['delivered_at'] = now();
                    break;
            }

            // Step 6: Update the order
            $order->update($updateData);

            Log::info('Order status updated', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $order->getOriginal('order_status'),
                'new_status' => $validated['order_status'],
                'updated_by' => Auth::id(),
            ]);

            // Step 7: Return success response
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'order_status' => $order->order_status,
                    'payment_status' => $order->payment_status,
                    'tracking_number' => $order->tracking_number,
                    'updated_at' => $order->updated_at,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Order status update failed', [
                'error' => $e->getMessage(),
                'order_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List orders for the current user
     */
    public function listOrders(Request $request)
    {
        try {
            // Step 1: Start building the query
            $query = Order::query();

            // Step 2: Filter by current user
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }

            // Step 3: Apply filters
            if ($request->has('payment_status')) {
                $query->whereHas('payment', function ($q) use ($request) {
                    $q->where('payment_status', $request->payment_status);
                });
            }

            if ($request->has('order_status')) {
                $query->where('order_status', $request->order_status);
            }

            // Step 4: Get paginated results
            $orders = $query->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));

            // Step 5: Enrich items with product data
            $enrichedOrders = $orders->getCollection()->map(function ($order) {
                $order->items = $this->enrichOrderItemsWithProductData($order->items);
                return $order;
            });
            $orders->setCollection($enrichedOrders);

            // Step 6: Return orders list
            return response()->json([
                'success' => true,
                'orders' => $orders,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enrich order items with latest product data
     */
    private function enrichOrderItemsWithProductData($items)
    {
        // Step 1: Check if items is an array
        if (!is_array($items)) {
            return $items;
        }

        // Step 2: Process each item
        return array_map(function ($item) {
            if (isset($item['id'])) {
                try {
                    // Step 3: Find product and update item data
                    $product = Product::with('category')->find($item['id']);
                    if ($product) {
                        $item['image'] = $product->image;
                        $item['description'] = $product->description;
                        $item['name'] = $product->title ?? $product->name ?? $item['name'];
                        $item['category'] = $product->category ? $product->category->name : null;
                        $item['brand'] = $product->brand;
                        $item['color'] = $product->color;
                        $item['discount'] = $product->discount;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to enrich order item with product data', [
                        'item_id' => $item['id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $item;
        }, $items);
    }

    /**
     * Admin: List all orders in the system
     */
    public function adminListOrders(Request $request)
    {
        try {
            // Step 1: Check admin permission
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Step 2: Build query
            $query = Order::query();

            // Step 3: Apply filters
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('payment_status')) {
                $query->whereHas('payment', function ($q) use ($request) {
                    $q->where('payment_status', $request->payment_status);
                });
            }

            if ($request->has('order_status')) {
                $query->where('order_status', $request->order_status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%");
                });
            }

            // Step 4: Get paginated results with user data
            $orders = $query->with('user')
                           ->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));

            // Step 5: Enrich items with product data
            $enrichedOrders = $orders->getCollection()->map(function ($order) {
                $order->items = $this->enrichOrderItemsWithProductData($order->items);
                return $order;
            });
            $orders->setCollection($enrichedOrders);

            // Step 6: Return orders list
            return response()->json([
                'success' => true,
                'orders' => $orders,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin: Get detailed information about a specific order
     */
    public function adminGetOrder(Request $request, $id)
    {
        try {
            // Step 1: Check admin permission
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Step 2: Find order with user relationship
            $order = Order::with('user')->findOrFail($id);

            // Step 3: Enrich items with product data
            $enrichedItems = $this->enrichOrderItemsWithProductData($order->items);

            // Step 4: Return detailed order information
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'invoice_number' => $order->invoice_number,
                    'user' => $order->user ? [
                        'id' => $order->user->id,
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                    ] : null,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'customer_email' => $order->customer_email,
                    'shipping_address' => $order->shipping_address,
                    'items' => $enrichedItems,
                    'subtotal' => $order->subtotal,
                    'discount_amount' => $order->discount_amount,
                    'shipping_amount' => $order->shipping_amount,
                    'total_amount' => $order->total_amount,
                    'currency' => $order->currency,
                    'formatted_total' => $order->formatted_total,
                    'promo_code' => $order->promo_code,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'payment_completed_at' => $order->payment_completed_at,
                    'confirmed_at' => $order->confirmed_at,
                    'processing_at' => $order->processing_at,
                    'shipped_at' => $order->shipped_at,
                    'delivered_at' => $order->delivered_at,
                    'tracking_number' => $order->tracking_number,
                    'notes' => $order->notes,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
    }
}
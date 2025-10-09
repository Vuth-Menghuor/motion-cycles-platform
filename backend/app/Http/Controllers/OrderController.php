<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Create a new order
     */
    public function createOrder(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|integer',
                'items.*.name' => 'required|string|max:255',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'subtotal' => 'required|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'shipping_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'promo_code' => 'nullable|string|max:50',
                'payment_method' => 'required|string|max:50',
                'bakong_account' => 'nullable|string|max:255',
                'account_name' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate order and invoice numbers
            $orderNumber = Order::generateOrderNumber();
            $invoiceNumber = Order::generateInvoiceNumber();

            // Create the order
            $order = Order::create([
                'order_number' => $orderNumber,
                'invoice_number' => $invoiceNumber,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'items' => $request->items,
                'subtotal' => $request->subtotal,
                'discount_amount' => $request->discount_amount ?? 0,
                'shipping_amount' => $request->shipping_amount ?? 0,
                'total_amount' => $request->total_amount,
                'currency' => $request->currency ?? 'USD',
                'promo_code' => $request->promo_code,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            // Generate QR code for Bakong payments
            if ($request->payment_method === 'Bakong KHQR') {
                // This would integrate with Bakong API to generate QR code
                // For now, we'll just store the account info
                $order->update([
                    'bakong_account' => $request->bakong_account ?? 'vuth_menghuor@aclb',
                    'account_name' => $request->account_name ?? 'MOTION CYCLE',
                ]);
            }

            Log::info('Order created successfully', ['order_id' => $order->id, 'order_number' => $orderNumber]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order' => $order,
                    'order_number' => $orderNumber,
                    'invoice_number' => $invoiceNumber,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Order creation failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List orders with pagination and filters
     */
    public function listOrders(Request $request): JsonResponse
    {
        try {
            $query = Order::query();

            // Apply filters
            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->has('order_status')) {
                $query->where('order_status', $request->order_status);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);

            $orders = $query->orderBy('created_at', 'desc')
                           ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $orders->items(),
                    'pagination' => [
                        'current_page' => $orders->currentPage(),
                        'last_page' => $orders->lastPage(),
                        'per_page' => $orders->perPage(),
                        'total' => $orders->total(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to list orders', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific order
     */
    public function getOrder($id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to get order', ['order_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status for an order
     */
    public function checkPaymentStatus($id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            // For Bakong payments, we would check with Bakong API
            // For now, we'll simulate payment checking
            $paymentStatus = $order->payment_status;

            // You would integrate with Bakong API here to check actual payment status
            // For demo purposes, we'll return the current status

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'payment_status' => $paymentStatus,
                    'order_status' => $order->order_status,
                    'payment_completed_at' => $order->payment_completed_at,
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to check payment status', ['order_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm payment for an order
     */
    public function confirmPayment(Request $request, $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Mark payment as completed
            $order->markAsPaymentCompleted([
                'confirmed_by' => 'admin', // You might want to get the actual user
                'confirmed_at' => now(),
                'notes' => $request->notes,
            ]);

            Log::info('Payment confirmed for order', ['order_id' => $order->id]);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully',
                'data' => [
                    'order' => $order
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to confirm payment', ['order_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

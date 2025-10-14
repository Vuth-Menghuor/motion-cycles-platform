<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\BakongApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Create a new order with payment processing
     */
    public function createOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'items' => 'required|array|min:1',
                'items.*.id' => 'required',
                'items.*.name' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
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

            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'invoice_number' => Order::generateInvoiceNumber(),
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'items' => $validated['items'],
                'subtotal' => $validated['subtotal'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'shipping_amount' => $validated['shipping_amount'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'currency' => $validated['currency'] ?? 'USD',
                'promo_code' => $validated['promo_code'] ?? null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            $response = [
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'invoice_number' => $order->invoice_number,
                'total_amount' => $order->total_amount,
                'currency' => $order->currency,
            ];

            // Generate KHQR if payment method is Bakong KHQR
            if ($validated['payment_method'] === 'Bakong KHQR') {
                $khqrResult = BakongApiService::generateIndividual(
                    $validated['bakong_account'],
                    $validated['account_name'],
                    $validated['total_amount'],
                    $validated['currency'] ?? 'USD',
                    true // Enable tracking
                );

                if ($khqrResult['success']) {
                    // Update order with QR data
                    $order->update([
                        'qr_code_string' => $khqrResult['qr_string'],
                        'qr_md5_hash' => $khqrResult['md5'] ?? null,
                        'payment_status' => 'pending',
                    ]);

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

            DB::commit();

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
     * Check payment status and update order
     */
    public function checkPaymentStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            // If payment is already completed, return success
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

            // Check payment status via Bakong API if MD5 is available
            if ($order->qr_md5_hash && $order->payment_method === 'Bakong KHQR') {
                $paymentResult = BakongApiService::checkPaymentStatus($order->qr_md5_hash);

                if ($paymentResult['success'] && $paymentResult['payment_status'] === 'completed') {
                    // Payment found - update order
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

            return response()->json([
                'success' => true,
                'payment_status' => $order->payment_status,
                'message' => 'Payment not completed yet',
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check failed', [
                'error' => $e->getMessage(),
                'order_id' => $validated['order_id'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manually confirm payment (for manual confirmation flow)
     */
    public function confirmPayment(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'notes' => 'nullable|string|max:500',
            ]);

            $order = Order::findOrFail($id);

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

            // Mark payment as completed manually
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
                'order_id' => $validated['order_id'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order details with invoice information
     */
    public function getOrder(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            // Optional: Check if user can access this order
            if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this order',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'invoice_number' => $order->invoice_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'customer_email' => $order->customer_email,
                    'items' => $order->items,
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
     * List orders (for admin or user's own orders)
     */
    public function listOrders(Request $request)
    {
        try {
            $query = Order::query();

            // If not admin, only show user's own orders
            if (Auth::check() && Auth::user()->role !== 'admin') {
                $query->where('user_id', Auth::id());
            }

            // Apply filters
            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->has('order_status')) {
                $query->where('order_status', $request->order_status);
            }

            $orders = $query->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));

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
}
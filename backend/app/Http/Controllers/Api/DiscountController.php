<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountController extends Controller
{
    /**
     * Get a list of discounts with optional filters
     * Supports filtering by active status, type, and search
     */
    public function index(Request $request)
    {
        // Step 1: Start building the query
        $query = Discount::query();

        // Step 2: Apply filters if provided
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Step 3: Order by creation date
        $query->orderBy('created_at', 'desc');

        // Step 4: Paginate results
        $discounts = $query->paginate($request->get('per_page', 15));

        // Step 5: Return paginated discounts
        return response()->json([
            'success' => true,
            'data' => $discounts,
        ]);
    }

    /**
     * Create a new discount
     * Validates input and saves to database
     */
    public function store(Request $request)
    {
        // Step 1: Validate the input data
        $request->validate([
            'code' => 'required|string|unique:discounts,code|max:50',
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'expire_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'integer|exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'integer|exists:categories,id',
        ]);

        // Step 2: Check percentage limit
        if ($request->type === 'percentage' && $request->value > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Percentage discount cannot exceed 100%',
            ], 422);
        }

        // Step 3: Create the discount
        $discount = Discount::create($request->all());

        // Step 4: Return success response
        return response()->json([
            'success' => true,
            'message' => 'Discount created successfully',
            'data' => $discount,
        ], 201);
    }

    /**
     * Get a single discount by ID
     */
    public function show(Discount $discount)
    {
        // Step 1: Return the discount data
        return response()->json([
            'success' => true,
            'data' => $discount,
        ]);
    }

    /**
     * Update an existing discount
     * Similar to store but allows updating existing records
     */
    public function update(Request $request, Discount $discount)
    {
        // Step 1: Validate the input data
        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('discounts')->ignore($discount->id)],
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'expire_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'integer|exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'integer|exists:categories,id',
        ]);

        // Step 2: Check percentage limit
        if ($request->type === 'percentage' && $request->value > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Percentage discount cannot exceed 100%',
            ], 422);
        }

        // Step 3: Update the discount
        $discount->update($request->all());

        // Step 4: Return success response
        return response()->json([
            'success' => true,
            'message' => 'Discount updated successfully',
            'data' => $discount,
        ]);
    }

    /**
     * Delete a discount
     * Only allows deletion if discount hasn't been used
     */
    public function destroy(Discount $discount)
    {
        // Step 1: Check if discount has been used
        if ($discount->used_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete discount that has been used',
            ], 422);
        }

        // Step 2: Delete the discount
        $discount->delete();

        // Step 3: Return success response
        return response()->json([
            'success' => true,
            'message' => 'Discount deleted successfully',
        ]);
    }

    /**
     * Check if a discount code is valid for a specific order
     * Validates the code and calculates discount amount
     */
    public function validateCode(Request $request)
    {
        // Step 1: Validate the input data
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer',
        ]);

        // Step 2: Clean and prepare data
        $code = strtoupper(trim($request->code));
        $orderAmount = $request->order_amount;
        $productIds = $request->product_ids ?? [];
        $categoryIds = $request->category_ids ?? [];

        // Step 3: Find the discount by code
        $discount = Discount::where('code', $code)->first();

        // Step 4: Check if discount exists
        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid discount code',
                'valid' => false,
            ], 404);
        }

        // Step 5: Check if discount is valid
        if (!$discount->isValid()) {
            $reason = '';
            if (!$discount->is_active) {
                $reason = 'Discount is inactive';
            } elseif (now()->toDateString() < $discount->start_date) {
                $reason = 'Discount has not started yet';
            } elseif (now()->toDateString() > $discount->expire_date) {
                $reason = 'Discount has expired';
            } elseif ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
                $reason = 'Discount usage limit exceeded';
            }

            return response()->json([
                'success' => false,
                'message' => $reason,
                'valid' => false,
            ], 400);
        }

        // Step 6: Check if discount applies to the products
        $applies = false;

        if (!$discount->applicable_products && !$discount->applicable_categories) {
            $applies = true;
        } else {
            if ($discount->applicable_products) {
                foreach ($productIds as $productId) {
                    if (in_array($productId, $discount->applicable_products)) {
                        $applies = true;
                        break;
                    }
                }
            }

            if (!$applies && $discount->applicable_categories) {
                foreach ($categoryIds as $categoryId) {
                    if (in_array($categoryId, $discount->applicable_categories)) {
                        $applies = true;
                        break;
                    }
                }
            }
        }

        // Step 7: If not applicable, return error
        if (!$applies) {
            return response()->json([
                'success' => false,
                'message' => 'Discount does not apply to selected products',
                'valid' => false,
            ], 400);
        }

        // Step 8: Calculate discount amount
        $discountAmount = $discount->calculateDiscount($orderAmount);

        // Step 9: Return success with discount details
        return response()->json([
            'success' => true,
            'message' => 'Discount code is valid',
            'valid' => true,
            'data' => [
                'id' => $discount->id,
                'code' => $discount->code,
                'name' => $discount->name,
                'type' => $discount->type,
                'value' => $discount->value,
                'discount_amount' => $discountAmount,
                'final_amount' => max(0, $orderAmount - $discountAmount),
            ],
        ]);
    }
}

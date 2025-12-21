<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Update the product's average rating and review count
     */
    private function updateProductRating($productId)
    {
        // Step 1: Find the product
        $product = Product::find($productId);
        if ($product) {
            // Step 2: Get review statistics
            $reviews = $product->reviews();
            $reviewCount = $reviews->count();
            $averageRating = $reviewCount > 0 ? $reviews->avg('rating') : 0;

            // Step 3: Update product with new rating data
            $product->update([
                'rating' => round($averageRating, 1),
                'review_count' => $reviewCount,
            ]);
        }
    }

    /**
     * Get all reviews for a specific product
     */
    public function index($productId)
    {
        // Step 1: Find the product
        $product = Product::findOrFail($productId);

        // Step 2: Get authenticated user reviews with user data
        $authReviews = $product->reviews()->where('is_guest', false)->with(['user' => function ($query) {
            $query->select('id', 'name');
        }])->get();

        // Step 3: Get guest reviews and format them
        $guestReviews = $product->reviews()->where('is_guest', true)->get()->map(function ($review) {
            return [
                'id' => $review->id,
                'user' => ['name' => $review->guest_name],
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at,
                'is_guest' => true,
            ];
        });

        // Step 4: Combine and sort all reviews by creation date
        $allReviews = collect([...$authReviews, ...$guestReviews])->sortByDesc('created_at')->values();

        // Step 5: Return combined reviews
        return response()->json($allReviews);
    }

    /**
     * Create a guest review for a product
     */
    public function storeGuest(Request $request, $productId)
    {
        // Step 1: Validate input data
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'user' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Step 2: Find the product
        $product = Product::findOrFail($productId);

        // Step 3: Create the guest review
        $review = Review::create([
            'product_id' => $productId,
            'user_id' => null,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'guest_name' => $request->user,
            'guest_email' => $request->email,
            'is_guest' => true,
        ]);

        // Step 4: Update product rating
        $this->updateProductRating($productId);

        // Step 5: Format and return the guest review
        $guestReview = [
            'id' => $review->id,
            'user' => $request->user,
            'email' => $request->email,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'date' => $review->created_at->toISOString(),
            'is_guest' => true,
        ];

        return response()->json($guestReview, 201);
    }

    /**
     * Create an authenticated user review for a product
     */
    public function store(Request $request, $productId)
    {
        // Step 1: Validate input data
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Step 2: Check if user is authenticated
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Step 3: Find the product
        $product = Product::findOrFail($productId);

        // Step 4: Check if user already reviewed this product
        $existingReview = Review::where('product_id', $productId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'You have already reviewed this product.'], 422);
        }

        // Step 5: Create the review
        $review = Review::create([
            'product_id' => $productId,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_guest' => false,
        ]);

        // Step 6: Update product rating
        $this->updateProductRating($productId);

        // Step 7: Return review with user data
        $reviewWithUser = $review->load(['user' => function ($query) {
            $query->select('id', 'name');
        }]);

        return response()->json($reviewWithUser, 201);
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, $productId, $reviewId)
    {
        // Step 1: Validate input data
        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string|max:1000',
        ]);

        // Step 2: Find the review
        $review = Review::where('product_id', $productId)->findOrFail($reviewId);

        // Step 3: Update the review
        $review->update($request->only(['rating', 'comment']));

        // Step 4: Update product rating
        $this->updateProductRating($productId);

        // Step 5: Return updated review
        return response()->json($review);
    }

    /**
     * Admin: Get all reviews with filtering and pagination
     */
    public function adminIndex(Request $request)
    {
        // Step 1: Build query with relationships
        $query = Review::with(['user' => function ($q) {
            $q->select('id', 'name');
        }, 'product' => function ($q) {
            $q->select('id', 'name', 'category_id', 'brand', 'image', 'pricing', 'description', 'rating', 'color');
        }, 'product.category' => function ($q) {
            $q->select('id', 'name');
        }]);

        // Step 2: Apply filters
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }
        if ($request->has('category') && $request->category) {
            $query->whereHas('product.category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }
        if ($request->has('brand') && $request->brand) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('brand', $request->brand);
            });
        }
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->has('customer') && $request->customer) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('name', 'like', '%' . $request->customer . '%');
                })->orWhere('guest_name', 'like', '%' . $request->customer . '%');
            });
        }

        // Step 3: Get paginated results
        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        // Step 4: Format reviews with customer names
        $formattedReviews = $reviews->getCollection()->map(function ($review) {
            $reviewArray = $review->toArray();

            if (!$review->user) {
                $reviewArray['customer_name'] = $review->guest_name ?: 'Anonymous';
                $reviewArray['customer_type'] = 'guest';
            } else {
                $reviewArray['customer_name'] = $review->user['name'];
                $reviewArray['customer_type'] = 'registered';
            }

            return $reviewArray;
        });

        $reviews->setCollection(collect($formattedReviews));

        // Step 5: Return formatted reviews
        return response()->json($reviews);
    }

    /**
     * Admin: Delete a review
     */
    public function adminDestroy($reviewId)
    {
        // Step 1: Find the review
        $review = Review::findOrFail($reviewId);
        $productId = $review->product_id;

        // Step 2: Delete the review
        $review->delete();

        // Step 3: Update product rating
        $this->updateProductRating($productId);

        // Step 4: Return success response
        return response()->json(['message' => 'Review deleted'], 204);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Update product's rating and review count
    private function updateProductRating($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $reviews = $product->reviews();
            $reviewCount = $reviews->count();
            $averageRating = $reviewCount > 0 ? $reviews->avg('rating') : 0;

            $product->update([
                'rating' => round($averageRating, 1),
                'review_count' => $reviewCount,
            ]);
        }
    }
    // Fetch reviews for a product (public)
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        $reviews = $product->reviews()->with(['user' => function ($query) {
            $query->select('id', 'name');
        }])->get();
        return response()->json($reviews);
    }

        // Submit a review (authenticated user)
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $product = Product::findOrFail($productId);
        $review = Review::create([
            'product_id' => $productId,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update product's rating and review count
        $this->updateProductRating($productId);

        // Load user with selected fields
        $review->load(['user' => function ($query) {
            $query->select('id', 'name');
        }]);

        return response()->json($review, 201);
    }

    // Update a review (admin only)
    public function update(Request $request, $productId, $reviewId)
    {
        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string|max:1000',
        ]);

        $review = Review::where('product_id', $productId)->findOrFail($reviewId);
        $review->update($request->only(['rating', 'comment']));

        // Update product's rating
        $this->updateProductRating($productId);

        return response()->json($review);
    }

    // Admin index with filters
    public function adminIndex(Request $request)
    {
        $query = Review::with(['user' => function ($q) {
            $q->select('id', 'name');
        }, 'product' => function ($q) {
            $q->select('id', 'name', 'category_id', 'brand', 'image', 'pricing', 'description', 'rating');
        }, 'product.category' => function ($q) {
            $q->select('id', 'name');
        }]);

        // Filters
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
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer . '%');
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($reviews);
    }

    // Admin delete review
    public function adminDestroy($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $productId = $review->product_id;
        $review->delete();

        // Update product's rating
        $this->updateProductRating($productId);

        return response()->json(['message' => 'Review deleted'], 204);
    }
}
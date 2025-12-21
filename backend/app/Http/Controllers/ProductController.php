<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Get all products with filtering, sorting, and pagination
     */
    public function getProducts(Request $request)
    {
        // Step 1: Start building the query with category relationship
        $query = Product::with('category');

        // Step 2: Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = strtolower($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(brand) LIKE ?', ['%' . $searchTerm . '%']);
            });
        }

        // Step 3: Apply category filter
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // Step 4: Apply brand filter
        if ($request->has('brand') && !empty($request->brand)) {
            $brands = explode(',', $request->brand);
            $query->whereIn('brand', $brands);
        }

        // Step 5: Apply color filter
        if ($request->has('color') && !empty($request->color)) {
            $colors = explode(',', $request->color);
            $query->whereIn('color', $colors);
        }

        // Step 6: Apply price range filters
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('pricing', '>=', $request->min_price);
        }
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('pricing', '<=', $request->max_price);
        }

        // Step 7: Apply rating filter
        if ($request->has('min_rating') && is_numeric($request->min_rating)) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Step 8: Apply discount filter
        if ($request->has('has_discount')) {
            $hasDiscountValue = $request->input('has_discount');
            if ($hasDiscountValue === 'true') {
                $query->whereNotNull('discount')->whereRaw("json_array_length(discount) > 0");
            } elseif ($hasDiscountValue === 'false') {
                $query->where(function ($q) {
                    $q->whereNull('discount')->orWhereRaw("json_array_length(discount) = 0");
                });
            }
        }

        // Step 9: Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['name', 'pricing', 'rating', 'created_at', 'brand'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Step 10: Apply pagination
        $perPage = $request->get('per_page', 20);
        $perPage = min(max($perPage, 1), 100);

        $products = $query->paginate($perPage);

        // Step 11: Transform product data for frontend
        $products->getCollection()->transform(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->name,
                'subtitle' => $product->description,
                'price' => $product->pricing,
                'brand' => $product->brand,
                'color' => $product->color,
                'quality' => $product->quality,
                'rating' => $product->rating,
                'reviewCount' => $product->review_count,
                'badge' => $product->badge,
                'discount' => $product->discount,
                'specs' => $product->specs,
                'image' => $product->image,
                'additionalImages' => is_array($product->additional_images) ?
                    $product->additional_images : (json_decode($product->additional_images, true) ?: []),
                'category' => $product->category,
                'description' => $product->description,
                'quantity' => $product->quantity,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        // Step 12: Return paginated products
        return response()->json($products);
    }

    /**
     * Create a new product
     */
    public function createProduct(Request $request)
    {
        // Step 1: Validate input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pricing' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
            'badge' => 'nullable|array',
            'discount' => 'nullable|array',
            'specs' => 'nullable',
            'additional_images' => 'nullable|array',
            'image' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            // Process images: convert data URLs to file paths
            if ($validated['image']) {
                $validated['image'] = $this->saveDataUrlAsFile($validated['image']);
            }

            if ($validated['additional_images'] && is_array($validated['additional_images'])) {
                foreach ($validated['additional_images'] as &$img) {
                    if (isset($img['url']) && str_starts_with($img['url'], 'data:')) {
                        $img['url'] = $this->saveDataUrlAsFile($img['url']);
                    }
                }
            }

            // Create the product
            $product = Product::create($validated);
            return response()->json($product, 201);

        } catch (\Exception $e) {
            Log::error('Error creating product:', ['error' => $e->getMessage(), 'data' => $validated]);
            return response()->json(['error' => 'Failed to create product', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a specific product by ID
     */
    public function getProduct($productId)
    {
        try {
            // Step 1: Find product with category relationship
            $product = Product::with('category')->findOrFail($productId);

            // Step 2: Format product data for frontend
            $formattedProduct = [
                'id' => $product->id,
                'title' => $product->name,
                'subtitle' => $product->description,
                'price' => $product->pricing,
                'brand' => $product->brand,
                'color' => $product->color,
                'quality' => $product->quality,
                'rating' => $product->rating,
                'reviewCount' => $product->review_count,
                'badge' => $product->badge,
                'discount' => $product->discount,
                'specs' => $product->specs,
                'image' => $product->image,
                'additionalImages' => is_array($product->additional_images) ?
                    $product->additional_images : (json_decode($product->additional_images, true) ?: []),
                'category' => $product->category,
                'description' => $product->description,
                'quantity' => $product->quantity,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];

            // Step 3: Return formatted product
            return response()->json($formattedProduct);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Step 4: Handle not found error
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    /**
     * Update an existing product
     */
    public function updateProduct(Request $request, $productId)
    {
        // Step 1: Find the product
        $product = Product::findOrFail($productId);

        // Step 2: Validate input data
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'pricing' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
            'badge' => 'nullable|array',
            'discount' => 'nullable|array',
            'specs' => 'nullable',
            'additional_images' => 'nullable|array',
            'image' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            // Process images: convert data URLs to file paths
            if (array_key_exists('image', $validated) && $validated['image'] && str_starts_with($validated['image'], 'data:')) {
                $validated['image'] = $this->saveDataUrlAsFile($validated['image']);
            }

            if (array_key_exists('additional_images', $validated)) {
                if (is_array($validated['additional_images'])) {
                    foreach ($validated['additional_images'] as &$img) {
                        if (is_string($img) && str_starts_with($img, 'data:')) {
                            $img = $this->saveDataUrlAsFile($img);
                        } elseif (is_object($img) && isset($img['url']) && str_starts_with($img['url'], 'data:')) {
                            $img['url'] = $this->saveDataUrlAsFile($img['url']);
                        }
                    }
                }
            }

            // Update the product
            $product->update($validated);
            return response()->json($product, 200);

        } catch (\Exception $e) {
            Log::error('Error updating product:', ['error' => $e->getMessage(), 'data' => $validated]);
            return response()->json(['error' => 'Failed to update product', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a product
     */
    public function deleteProduct($productId)
    {
        // Step 1: Find the product
        $product = Product::findOrFail($productId);

        // Step 2: Delete the product
        $product->delete();

        // Step 3: Return empty response
        return response()->json(null, 204);
    }

    /**
     * Save data URL as file and return the path    
     */
    
    private function saveDataUrlAsFile($dataUrl, $folder = 'images')
    {
        // Return if not a data URL
        if (!str_starts_with($dataUrl, 'data:')) {
            return $dataUrl;
        }

        // Extract data from data URL
        $parts = explode(',', $dataUrl);
        $mime = explode(';', $parts[0])[0];
        $extension = explode('/', $mime)[1];

        // Create unique filename and path
        $filename = uniqid() . '.' . $extension;
        $path = $folder . '/' . $filename;

        // Save file and return public URL
        Storage::disk('public')->put($path, base64_decode($parts[1]));
        return url('/storage/' . $path);
    }
}

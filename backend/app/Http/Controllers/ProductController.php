<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * --- Get /api/products
     */
    public function getProducts()
    {
        $products = Product::with('category')->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->name,
                'subtitle' => $product->description,
                'price' => $product->pricing,
                'brand' => $product->brand,
                'color' => $product->color,
                'rating' => $product->rating,
                'reviewCount' => $product->review_count,
                'badge' => $product->badge,
                'discount' => $product->discount,
                'specs' => $product->specs,
                'image' => $product->image,
                'additionalImages' => is_array($product->additional_images) ? $product->additional_images : (json_decode($product->additional_images, true) ?: []),
                'category' => $product->category,
                'description' => $product->description,
                'quantity' => $product->quantity,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json($products);
    }

    /**
     * --- Post /api/products
     */
    public function createProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pricing' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
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
            // Process images - save data URLs as files
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

            $product = Product::create($validated);
            return response()->json($product, 201);
        } catch (\Exception $e) {
            Log::error('Error creating product:', ['error' => $e->getMessage(), 'data' => $validated]);
            return response()->json(['error' => 'Failed to create product', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get /api/products/{id}
     */
    public function getProduct($productId)
    {
        try {
            $product = Product::with('category')->findOrFail($productId);
            
            $formattedProduct = [
                'id' => $product->id,
                'title' => $product->name,
                'subtitle' => $product->description,
                'price' => $product->pricing,
                'brand' => $product->brand,
                'color' => $product->color,
                'rating' => $product->rating,
                'reviewCount' => $product->review_count,
                'badge' => $product->badge,
                'discount' => $product->discount,
                'specs' => $product->specs,
                'image' => $product->image,
                'additionalImages' => is_array($product->additional_images) ? $product->additional_images : (json_decode($product->additional_images, true) ?: []),
                'category' => $product->category,
                'description' => $product->description,
                'quantity' => $product->quantity,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];

            return response()->json($formattedProduct);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    /**
     * --- Patch /api/products/{id}
     */
    public function updateProduct(Request $request ,$productId)
    {
        $product = Product::findOrFail($productId);
        $product->update($request->all());
        return response()->json($product, 200);
    }

    /**
     * --- Delete /api/products/{id
     */
    public function deleteProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $product->delete();
        return response()->json(null, 204);
    }

    /**
     * Save data URL as file and return the path
     */
    private function saveDataUrlAsFile($dataUrl, $folder = 'images')
    {
        if (!str_starts_with($dataUrl, 'data:')) {
            return $dataUrl;
        }

        $parts = explode(',', $dataUrl);
        $mime = explode(';', $parts[0])[0];
        $extension = explode('/', $mime)[1];
        $filename = uniqid() . '.' . $extension;
        $path = $folder . '/' . $filename;
        $data = base64_decode($parts[1]);
        Storage::disk('public')->put($path, $data);
        Log::info('Saved image to ' . $path);
        return url('/storage/' . $path);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'brand' => 'required|string|max:255',
        'price' => 'required|numeric',
        'description' => 'nullable|string',
        'image' => 'nullable|url',
        'additionalImages' => 'nullable|array',
        'specs' => 'nullable|array',
        'category_id' => 'required|exists:categories,id',
        'discount' => 'nullable|array',
    ]);

    $product = Product::create($validated);
    return response()->json($product, 201);
}
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * --- Get /api/products
     */
    public function getProducts()
    {
        return Product::all();
    }

    /**
     * --- Post /api/products
     */
    public function createProduct(Request $request)
    {
        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    /**
     * Get /api/products/{id}
     */
    public function getProduct($productId)
    {
        $product = Product::findOrFail($productId);
        return response()->json($product);
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
}

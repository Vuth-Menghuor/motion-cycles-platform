<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function getCategories()
    {
        // Step 1: Get all categories from database
        return Category::all();
    }

    /**
     * Create a new category
     */
    public function createCategory(Request $request)
    {
        // Step 1: Create new category with request data
        $category = Category::create($request->all());

        // Step 2: Return created category with 201 status
        return response()->json($category, 201);
    }

    /**
     * Get a specific category by ID
     */
    public function getCategory($categoryId)
    {
        // Step 1: Find category by ID or fail if not found
        $category = Category::findOrFail($categoryId);

        // Step 2: Return the category
        return response()->json($category);
    }

    /**
     * Update an existing category
     */
    public function updateCategory(Request $request, $categoryId)
    {
        // Step 1: Find category by ID or fail if not found
        $category = Category::findOrFail($categoryId);

        // Step 2: Update category with request data
        $category->update($request->all());

        // Step 3: Return updated category
        return response()->json($category, 200);
    }

    /**
     * Delete a category
     */
    public function deleteCategory($categoryId)
    {
        // Step 1: Find category by ID or fail if not found
        $category = Category::findOrFail($categoryId);

        // Step 2: Delete the category
        $category->delete();

        // Step 3: Return empty response with 204 status
        return response()->json(null, 204);
    }
}

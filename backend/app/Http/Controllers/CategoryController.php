<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * --- Get /api/categories
     */
    public function getCategories()
    {
        return Category::all();
    }

    /**
     * --- Post /api/categories
     */
    public function createCategory(Request $request)
    {
        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    /**
     * --- Get /api/categories/{id}
     */
    public function getCategory($categoryId)
    {
        $category = Category::findOrfail($categoryId);
        return response()->json($category);
    }

    /**
     * --- Patch /api/categories/{categoryId}
     */
    public function updateCategory(Request $request, $categoryId)
    {
        $category = Category::findOrfail($categoryId);
        $category->update($request->all());
        return response()->json($category, 200);
    }

    /**
     * --- Delete /api/categories/{id}
     */
    public function deleteCategory($categoryId)
    {
        $category = Category::findOrfail($categoryId);
        $category->delete();
        return response()->json(null, 204);
    }
}

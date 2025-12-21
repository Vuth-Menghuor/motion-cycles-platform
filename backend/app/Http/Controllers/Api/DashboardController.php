<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get basic dashboard statistics
     * Returns total products, revenue, and customers
     */
    public function getStats()
    {
        try {
            // Step 1: Count total products in the database
            $totalProducts = Product::count();

            // Step 2: Sum up all product prices for total revenue
            $totalRevenue = Product::sum('pricing');

            // Step 3: Count total customers (users)
            $totalCustomers = User::count();

            // Step 4: Return the statistics in JSON format
            return response()->json([
                'success' => true,
                'data' => [
                    'total_products' => $totalProducts,
                    'total_revenue' => $totalRevenue,
                    'total_customers' => $totalCustomers,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products that are running low on stock
     * Shows products below the threshold level
     */
    public function getStockAlerts(Request $request)
    {
        try {
            // Step 1: Get the stock threshold from request (default 10)
            $lowStockThreshold = $request->get('threshold', 10);

            // Step 2: Query products with low stock
            $lowStockProducts = Product::with('category')
                ->where('quantity', '<=', $lowStockThreshold)
                ->where('quantity', '>', 0)
                ->orderBy('quantity', 'asc')
                ->get()
                ->map(function ($product) use ($lowStockThreshold) {
                    // Step 3: Format each product data
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'brand' => $product->brand,
                        'category' => $product->category ? $product->category->name : 'Unknown',
                        'current_stock' => $product->quantity,
                        'min_stock' => $lowStockThreshold,
                        'status' => $this->getStockStatus($product->quantity, $lowStockThreshold),
                        'stock_alert' => $this->getStockAlert($product->quantity, $lowStockThreshold),
                        'last_updated' => $product->updated_at->format('Y-m-d'),
                    ];
                });

            // Step 4: Return the low stock products list
            return response()->json([
                'success' => true,
                'data' => $lowStockProducts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stock alerts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get distribution of products across categories
     * Shows how many products are in each category with percentages
     */
    public function getCategoryDistribution()
    {
        try {
            // Step 1: Get all categories with their product count
            $categories = Category::withCount('products')
                ->orderBy('products_count', 'desc')
                ->get();

            // Step 2: Calculate total products across all categories
            $totalProducts = $categories->sum('products_count');

            // Step 3: If no products, return empty array
            if ($totalProducts === 0) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Step 4: Calculate percentage for each category
            $distribution = $categories->map(function ($category) use ($totalProducts) {
                $percentage = round(($category->products_count / $totalProducts) * 100, 1);
                return [
                    'name' => $category->name,
                    'count' => $category->products_count,
                    'percentage' => $percentage,
                    'color' => $this->getCategoryColor($category->name)
                ];
            });

            // Step 5: Return the category distribution data
            return response()->json([
                'success' => true,
                'data' => $distribution
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category distribution',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock status based on quantity and threshold
     * Returns 'LOW', 'Normal', or 'FULL'
     */
    private function getStockStatus($quantity, $threshold)
    {
        if ($quantity <= $threshold / 2) {
            return 'LOW';
        } elseif ($quantity <= $threshold) {
            return 'Normal';
        } else {
            return 'FULL';
        }
    }

    /**
     * Get stock alert message based on quantity and threshold
     * Returns appropriate alert message
     */
    private function getStockAlert($quantity, $threshold)
    {
        if ($quantity <= $threshold / 2) {
            return 'Restock Needed';
        } elseif ($quantity <= $threshold) {
            return 'Monitor';
        } else {
            return 'Well Stock';
        }
    }

    /**
     * Get color for category chart based on category name
     * Returns hex color code
     */
    private function getCategoryColor($categoryName)
    {
        // Predefined colors for different bike categories
        $colors = [
            'Mountain Bike' => '#42A5F5',
            'Road Bike' => '#EF5350',
            'Hybrid Bike' => '#66BB6A',
            'Electric Bike' => '#AB47BC',
            'Kids Bike' => '#FF9800',
            'BMX' => '#26A69A',
            'Folding Bike' => '#EC407A',
            'Cruiser' => '#8D6E63',
        ];

        // Return color for category, or default gray if not found
        return $colors[$categoryName] ?? '#78909C';
    }
}
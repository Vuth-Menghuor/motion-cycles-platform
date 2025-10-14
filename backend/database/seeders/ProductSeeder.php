<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $mountainBikeCategory = Category::where('name', 'Mountain Bike')->first();
        $roadBikeCategory = Category::where('name', 'Road Bike')->first();

        // Sample Mountain Bikes
        Product::create([
            'name' => 'EcoRide X5 Electric Bike',
            'description' => 'High-performance electric mountain bike with 500W motor and 75km range. Perfect for off-road adventures.',
            'pricing' => 1999.99,
            'category_id' => $mountainBikeCategory->id,
            'brand' => 'EcoRide',
            'color' => 'Black',
            'rating' => 4.5,
            'review_count' => 128,
            'badge' => [
                [
                    'text' => '50% OFF',
                    'gradient' => 'from-orange-400 to-red-500'
                ]
            ],
            'discount' => [
                [
                    'type' => 'percent',
                    'value' => 50,
                    'code' => null,
                    'start_date' => null,
                    'expire_date' => null
                ]
            ],
            'specs' => [
                'range' => '75km',
                'hubMotor' => '500W',
                'payload' => '120kg',
                'controller' => 'LCD Display',
                'weight' => '25kg',
                'display' => 'Color LCD'
            ],
            'additional_images' => [],
            'image' => '/images/eco-ride-x5.jpg',
            'quantity' => 10
        ]);

        Product::create([
            'name' => 'TrailMaster Pro',
            'description' => 'Professional mountain bike designed for extreme trails with full suspension and carbon frame.',
            'pricing' => 2499.99,
            'category_id' => $mountainBikeCategory->id,
            'brand' => 'TrailMaster',
            'color' => 'Red',
            'rating' => 4.8,
            'review_count' => 95,
            'badge' => [],
            'discount' => [],
            'specs' => [
                'range' => 'N/A',
                'hubMotor' => 'N/A',
                'payload' => '110kg',
                'controller' => 'N/A',
                'weight' => '12kg',
                'display' => 'N/A'
            ],
            'additional_images' => [],
            'image' => '/images/trailmaster-pro.jpg',
            'quantity' => 5
        ]);

        // Sample Road Bikes
        Product::create([
            'name' => 'SpeedRider Carbon',
            'description' => 'Ultra-light carbon road bike designed for speed and long-distance cycling.',
            'pricing' => 1899.99,
            'category_id' => $roadBikeCategory->id,
            'brand' => 'SpeedRider',
            'color' => 'Blue',
            'rating' => 4.6,
            'review_count' => 67,
            'badge' => [
                [
                    'text' => '30% OFF',
                    'gradient' => 'from-green-400 to-blue-500'
                ]
            ],
            'discount' => [
                [
                    'type' => 'percent',
                    'value' => 30,
                    'code' => null,
                    'start_date' => null,
                    'expire_date' => null
                ]
            ],
            'specs' => [
                'range' => 'N/A',
                'hubMotor' => 'N/A',
                'payload' => '100kg',
                'controller' => 'N/A',
                'weight' => '8.5kg',
                'display' => 'N/A'
            ],
            'additional_images' => [],
            'image' => '/images/speedrider-carbon.jpg',
            'quantity' => 8
        ]);
    }
}

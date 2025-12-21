<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get categories
        $mountainBikeCategory = Category::where('name', 'Mountain Bike')->first();
        $roadBikeCategory = Category::where('name', 'Road Bike')->first();

        Product::create([
            'name' => 'Cannondale Trail Neo 2 Electric Mountain Bike',
            'description' => 'A powerful and versatile electric mountain bike built for off-road adventure. Equipped with a 500Wh Bosch battery, advanced aluminum frame, and smooth suspension for ultimate comfort.',
            'pricing' => 25.99,
            'category_id' => $mountainBikeCategory->id,
            'brand' => 'Cannondale',
            'color' => 'Forest Green',
            'rating' => 4.7,
            'review_count' => 180,
            'badge' => [
                [
                    'text' => 'NEW ARRIVAL',
                    'gradient' => 'from-lime-400 to-green-600'
                ]
            ],
            'discount' => [
                [
                    'type' => 'percent',
                    'value' => 10,
                    'code' => 'CANNON10',
                    'start_date' => null,
                    'expire_date' => null
                ]
            ],
            'specs' => [
                'range' => '100km',
                'hubMotor' => '500W Bosch Drive Unit',
                'payload' => '120kg',
                'controller' => 'Bosch Purion Display',
                'weight' => '22.8kg',
                'display' => 'LCD Compact Display'
            ],
            'additional_images' => [
                '/images/mount_2/mount_2_alt1.png',
                '/images/mount_2/mount_2_alt2.png'
            ],
            'image' => '/images/mount_1.png',
            'quantity' => 10
        ]);

        Product::create([
            'name' => 'Bianchi Aria E-Road Carbon Electric Bike',
            'description' => 'A sleek, aerodynamic electric road bike combining performance and style. Designed with a lightweight carbon frame and integrated battery system for smooth long-distance rides.',
            'pricing' => 29.99,
            'category_id' => $roadBikeCategory->id,
            'brand' => 'Bianchi',
            'color' => 'Celeste Blue',
            'rating' => 4.9,
            'review_count' => 310,
            'badge' => [
                [
                    'text' => 'HOT DEAL',
                    'gradient' => 'from-blue-400 to-teal-500'
                ]
            ],
            'discount' => [
                [
                    'type' => 'percent',
                    'value' => 15,
                    'code' => 'BIANCHI15',
                    'start_date' => null,
                    'expire_date' => null
                ]
            ],
            'specs' => [
                'range' => '120km',
                'hubMotor' => '250W Ebikemotion X35+',
                'payload' => '110kg',
                'controller' => 'Integrated One-Touch Control',
                'weight' => '11.8kg',
                'display' => 'LED Display System'
            ],
            'additional_images' => [
                '/images/road_2.png',
                '/images/road_3.png'
            ],
            'image' => '/images/road_1.png',
            'quantity' => 8
        ]);

        Product::create([
            'name' => 'Giant Explore E+ 3 Electric Trekking Bike',
            'description' => 'Built for comfort and endurance, the Giant Explore E+ 3 offers smooth power delivery, strong battery life, and an ergonomic design perfect for city rides or weekend adventures.',
            'pricing' => 22.50,
            'category_id' => $mountainBikeCategory->id,
            'brand' => 'Giant',
            'color' => 'Charcoal Gray',
            'rating' => 4.6,
            'review_count' => 205,
            'badge' => [
                [
                    'text' => 'LIMITED STOCK',
                    'gradient' => 'from-orange-400 to-red-500'
                ]
            ],
            'discount' => [
                [
                    'type' => 'percent',
                    'value' => 5,
                    'code' => 'GIANT5',
                    'start_date' => null,
                    'expire_date' => null
                ]
            ],
            'specs' => [
                'range' => '90km',
                'hubMotor' => '400W SyncDrive Core Motor',
                'payload' => '125kg',
                'controller' => 'RideControl Dash',
                'weight' => '24.5kg',
                'display' => 'Smart LCD Display'
            ],
            'additional_images' => [
                '/images/mount_2/mount_2_alt3.png',
                '/images/mount_2/mount_2_alt4.png'
            ],
            'image' => '/images/mount_3.png',
            'quantity' => 20
        ]);

        Product::create([
            'name' => 'Specialized Turbo Vado SL 5.0 Electric Bike',
            'description' => 'Lightweight and responsive electric bike perfect for urban commuting. Features a 320Wh battery, sleek frame, and advanced Turbo SL motor technology.',
            'pricing' => 27.99,
            'category_id' => $mountainBikeCategory->id,
            'brand' => 'Specialized',
            'color' => 'Brushed Silver',
            'rating' => 4.9,
            'review_count' => 415,
            'badge' => [
                [
                    'text' => 'TOP RATED',
                    'gradient' => 'from-yellow-400 to-amber-600'
                ]
            ],
            'discount' => [
                [
                    'type' => 'percent',
                    'value' => 12,
                    'code' => 'SPEC12',
                    'start_date' => null,
                    'expire_date' => null
                ]
            ],
            'specs' => [
                'range' => '130km',
                'hubMotor' => '240W Specialized SL 1.1',
                'payload' => '115kg',
                'controller' => 'Turbo Connect Unit',
                'weight' => '14.9kg',
                'display' => 'Compact LCD Display'
            ],
            'additional_images' => [
                '/images/road_1.png',
                '/images/road_2.png'
            ],
            'image' => '/images/road_3.png',
            'quantity' => 12
        ]);
    }
}

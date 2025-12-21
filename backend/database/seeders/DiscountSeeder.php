<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            [
                'code' => 'BOOKRIDE50',
                'name' => 'Book Ride 50% Off',
                'type' => 'percentage',
                'value' => 5.0, // Changed to 5% for testing
                'min_order_amount' => 50.0,
                'usage_limit' => null, // Unlimited
                'start_date' => now()->toDateString(),
                'expire_date' => now()->addDays(30)->toDateString(),
                'is_active' => true,
                'applicable_products' => null, // All products
                'applicable_categories' => null, // All categories
            ],
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount 10%',
                'type' => 'percentage',
                'value' => 10.0,
                'min_order_amount' => 25.0,
                'usage_limit' => 100,
                'start_date' => now()->toDateString(),
                'expire_date' => now()->addDays(60)->toDateString(),
                'is_active' => true,
                'applicable_products' => null,
                'applicable_categories' => null,
            ],
            [
                'code' => 'FIXED20',
                'name' => 'Fixed $20 Off',
                'type' => 'fixed',
                'value' => 20.0,
                'min_order_amount' => 50.0, // Changed from 100.0 to 50.0
                'usage_limit' => 50,
                'start_date' => now()->toDateString(),
                'expire_date' => now()->addDays(14)->toDateString(),
                'is_active' => true,
                'applicable_products' => null,
                'applicable_categories' => null,
            ],
        ];

        foreach ($discounts as $discount) {
            Discount::create($discount);
        }
    }
}

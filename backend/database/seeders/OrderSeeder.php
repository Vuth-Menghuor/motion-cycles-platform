<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('username', 'admin')->first();
        $products = Product::all();

        if ($products->isEmpty()) {
            return;
        }

        // Create some sample orders with different dates
        $dates = [
            now()->subDays(10),
            now()->subDays(8),
            now()->subDays(5),
            now()->subDays(3),
            now()->subDays(1),
        ];

        foreach ($dates as $date) {
            // Add some random products to the order
            $selectedProducts = $products->random(min(3, $products->count()));
            $subtotal = 0;
            $orderItems = [];

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->pricing;
                $itemTotal = $price * $quantity;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $itemTotal,
                ];

                $subtotal += $itemTotal;
            }

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'customer_name' => 'Sample Customer',
                'customer_email' => 'customer@example.com',
                'customer_phone' => '1234567890',
                'shipping_address' => ['address' => '123 Sample Street', 'city' => 'Sample City', 'country' => 'Sample Country'],
                'items' => $orderItems,
                'subtotal' => $subtotal,
                'shipping_amount' => 10.00,
                'discount_amount' => 0,
                'total_amount' => $subtotal + 10.00,
                'order_status' => 'delivered',
                'payment_status' => 'completed',
                'payment_method' => 'cash',
                'notes' => 'Sample order',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Payment;

class BackfillPaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all orders that don't have a payment record yet
        $orders = Order::whereDoesntHave('payment')->get();

        foreach ($orders as $order) {
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'qr_code_string' => $order->qr_code_string,
                'qr_md5_hash' => $order->qr_md5_hash,
                'payment_gateway_string' => null, // Will be filled when payment completes
                'payment_data' => null, // Will be filled with transaction data
                'payment_completed_at' => $order->payment_completed_at,
                'amount' => $order->total_amount,
                'currency' => $order->currency ?? 'USD',
            ]);
        }

        $this->command->info('Backfilled ' . $orders->count() . ' payment records from existing orders.');
    }
}

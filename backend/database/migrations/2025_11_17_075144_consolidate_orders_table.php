<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration consolidates all orders table changes
        // It replaces: create_orders_table, add_order_tracking_fields_to_orders_table,
        // add_shipping_address_to_orders_table, remove_payment_columns_from_orders_table

        Schema::table('orders', function (Blueprint $table) {
            // Add missing columns that were added in subsequent migrations
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->json('shipping_address')->nullable()->after('customer_email');
            }
            if (!Schema::hasColumn('orders', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('payment_completed_at');
            }
            if (!Schema::hasColumn('orders', 'processing_at')) {
                $table->timestamp('processing_at')->nullable()->after('confirmed_at');
            }
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('processing_at');
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number', 100)->nullable()->after('delivered_at');
            }

            // Note: Payment columns were removed in a later migration and moved to payments table
            // We don't need to add them back here
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove added columns
            $columnsToDrop = ['shipping_address', 'confirmed_at', 'processing_at', 'shipped_at', 'delivered_at', 'tracking_number'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

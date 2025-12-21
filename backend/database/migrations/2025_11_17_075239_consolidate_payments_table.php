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
        // This migration consolidates all payments table changes
        // It replaces: create_payments_table, rename_transaction_id_to_payment_gateway_string_in_payments_table
        // The payments table was created with the final schema, only the column rename happened later
        // No changes needed here as the table is already in final state
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes to reverse as this is just documentation
    }
};

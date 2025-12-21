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
        Schema::table('payments', function (Blueprint $table) {
            // Drop the existing unique index on transaction_id
            $table->dropUnique(['transaction_id']);
            
            // Rename the column
            $table->renameColumn('transaction_id', 'payment_gateway_string');
            
            // Add the unique index back on the new column name
            $table->unique('payment_gateway_string');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the unique index on payment_gateway_string
            $table->dropUnique(['payment_gateway_string']);
            
            // Rename the column back
            $table->renameColumn('payment_gateway_string', 'transaction_id');
            
            // Add the unique index back on the old column name
            $table->unique('transaction_id');
        });
    }
};

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
        // This migration consolidates all discounts table changes
        // It replaces: create_discounts_table, make_discount_name_nullable

        Schema::table('discounts', function (Blueprint $table) {
            // Make name column nullable
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            // Make name column not nullable again
            $table->string('name')->nullable(false)->change();
        });
    }
};

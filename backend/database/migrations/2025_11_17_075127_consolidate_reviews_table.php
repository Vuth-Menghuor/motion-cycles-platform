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
        // This migration consolidates all reviews table changes
        // It replaces: create_reviews_table, add_guest_fields_to_reviews_table, make_user_id_nullable_in_reviews_table

        Schema::table('reviews', function (Blueprint $table) {
            // Add missing columns that were added in subsequent migrations
            if (!Schema::hasColumn('reviews', 'guest_name')) {
                $table->string('guest_name')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'guest_email')) {
                $table->string('guest_email')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'is_guest')) {
                $table->boolean('is_guest')->default(false);
            }

            // Make user_id nullable
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Remove added columns
            $columnsToDrop = ['guest_name', 'guest_email', 'is_guest'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('reviews', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Make user_id not nullable again
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};

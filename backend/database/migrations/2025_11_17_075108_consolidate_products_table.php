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
        // This migration consolidates all products table changes into the final schema
        // It replaces: create_products, add_fields_to_products_table, change_image_column_to_text,
        // add_quantity_to_products_table, add_quality_to_products_table

        Schema::table('products', function (Blueprint $table) {
            // Add missing columns that were added in subsequent migrations
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable();
            }
            if (!Schema::hasColumn('products', 'color')) {
                $table->string('color')->nullable();
            }
            if (!Schema::hasColumn('products', 'rating')) {
                $table->decimal('rating', 3, 1)->default(0);
            }
            if (!Schema::hasColumn('products', 'review_count')) {
                $table->integer('review_count')->default(0);
            }
            if (!Schema::hasColumn('products', 'badge')) {
                $table->json('badge')->nullable();
            }
            if (!Schema::hasColumn('products', 'discount')) {
                $table->json('discount')->nullable();
            }
            if (!Schema::hasColumn('products', 'specs')) {
                $table->json('specs')->nullable();
            }
            if (!Schema::hasColumn('products', 'additional_images')) {
                $table->json('additional_images')->nullable();
            }
            if (!Schema::hasColumn('products', 'quantity')) {
                $table->integer('quantity')->default(1);
            }
            if (!Schema::hasColumn('products', 'quality')) {
                $table->string('quality')->nullable();
            }

            // Change image column to text if it's still string
            $table->text('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove all the added columns
            $columnsToDrop = ['brand', 'color', 'rating', 'review_count', 'badge', 'discount', 'specs', 'additional_images', 'quantity', 'quality'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Change image back to string
            $table->string('image')->nullable()->change();
        });
    }
};

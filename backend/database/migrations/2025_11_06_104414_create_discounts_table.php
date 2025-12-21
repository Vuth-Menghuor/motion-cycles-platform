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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Discount code (e.g., 'BOOKRIDE50')
            $table->string('name'); // Human readable name
            $table->enum('type', ['percentage', 'fixed']); // Type of discount
            $table->decimal('value', 10, 2); // Discount value (percentage or fixed amount)
            $table->decimal('min_order_amount', 10, 2)->nullable(); // Minimum order amount to apply discount
            $table->integer('usage_limit')->nullable(); // Maximum number of times this discount can be used
            $table->integer('used_count')->default(0); // How many times it's been used
            $table->date('start_date'); // When the discount becomes active
            $table->date('expire_date'); // When the discount expires
            $table->boolean('is_active')->default(true); // Whether the discount is active
            $table->json('applicable_products')->nullable(); // Specific product IDs this discount applies to (null means all products)
            $table->json('applicable_categories')->nullable(); // Specific category IDs this discount applies to
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'discounts';

    // Fields that can be mass assigned
    protected $fillable = [
        'code',                   // Unique discount code
        'name',                   // Discount name
        'type',                   // 'percentage' or 'fixed'
        'value',                  // Discount value (percentage or amount)
        'min_order_amount',       // Minimum order amount to apply discount
        'usage_limit',            // Maximum number of uses
        'used_count',             // Current number of uses
        'start_date',             // When discount becomes active
        'expire_date',            // When discount expires
        'is_active',              // Whether discount is active
        'applicable_products',    // Array of product IDs this discount applies to
        'applicable_categories',  // Array of category IDs this discount applies to
    ];

    // Type casting for attributes
    protected $casts = [
        'value' => 'decimal:2',           // Decimal with 2 places
        'min_order_amount' => 'decimal:2', // Decimal with 2 places
        'start_date' => 'date',           // Cast to date
        'expire_date' => 'date',          // Cast to date
        'is_active' => 'boolean',         // Cast to boolean
        'applicable_products' => 'array', // Cast to array
        'applicable_categories' => 'array', // Cast to array
    ];

    // Check if the discount is currently valid
    public function isValid()
    {
        $now = now();

        return $this->is_active &&
               $now->gte($this->start_date) &&
               $now->lte($this->expire_date) &&
               ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    // Check if discount applies to a specific product
    public function appliesToProduct($productId, $categoryId = null)
    {
        // If no restrictions, applies to all
        if (!$this->applicable_products && !$this->applicable_categories) {
            return true;
        }

        // Check specific products
        if ($this->applicable_products && in_array($productId, $this->applicable_products)) {
            return true;
        }

        // Check categories
        if ($this->applicable_categories && $categoryId && in_array($categoryId, $this->applicable_categories)) {
            return true;
        }

        return false;
    }

    // Calculate discount amount for a given price
    public function calculateDiscount($price)
    {
        if ($this->type === 'percentage') {
            return $price * ($this->value / 100);
        } else {
            return min($price, $this->value);
        }
    }
}

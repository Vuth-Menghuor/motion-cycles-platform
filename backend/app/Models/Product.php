<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'products';

    // Fields that can be mass assigned
    protected $fillable = [
        'name',               // Product name
        'description',        // Product description
        'pricing',            // Product price
        'category_id',        // ID of the category this product belongs to
        'brand',              // Product brand
        'color',              // Product color
        'quality',            // Product quality rating
        'rating',             // Average customer rating
        'review_count',       // Number of reviews
        'badge',              // Special badges as array
        'discount',           // Discount information as array
        'specs',              // Product specifications as array
        'additional_images',  // Additional product images as array
        'image',              // Main product image
        'quantity',           // Available quantity in stock
        'created_at',         // When product was created
        'updated_at'          // When product was last updated
    ];

    // Type casting for attributes
    protected $casts = [
        'rating' => 'decimal:1',           // Decimal with 1 place
        'pricing' => 'decimal:2',          // Decimal with 2 places
        'badge' => 'array',                // Cast to array
        'discount' => 'array',             // Cast to array
        'specs' => 'array',                // Cast to array
        'additional_images' => 'array',    // Cast to array
    ];

    // Product belongs to a Category (each product belongs to one category)
    public function category()
    {
        // Step 1: Return belongsTo relationship to Category model
        return $this->belongsTo(Category::class);
    }

    // Product has many Reviews (one product can have multiple reviews)
    public function reviews()
    {
        // Step 1: Return hasMany relationship to Review model
        return $this->hasMany(Review::class);
    }

    // Product can be in many Carts (one product can be added to multiple shopping carts)
    public function carts()
    {
        // Step 1: Return hasMany relationship to Cart model
        return $this->hasMany(Cart::class);
    }
}

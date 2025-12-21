<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Fields that can be mass assigned
    protected $fillable = [
        'product_id',   // ID of the product being reviewed
        'user_id',      // ID of the user who wrote the review (null for guests)
        'rating',       // Rating given (1-5 stars)
        'comment',      // Review comment text
        'guest_name',   // Name of guest reviewer
        'guest_email',  // Email of guest reviewer
        'is_guest'      // Whether this is a guest review
    ];

    // Type casting for attributes
    protected $casts = [
        'is_guest' => 'boolean', // Cast to boolean
    ];

    // Review belongs to a Product (each review belongs to one product)
    public function product()
    {
        // Step 1: Return belongsTo relationship to Product model
        return $this->belongsTo(Product::class);
    }

    // Review belongs to a User (each review belongs to one user, can be null for guests)
    public function user()
    {
        // Step 1: Return belongsTo relationship to User model
        return $this->belongsTo(User::class);
    }
}

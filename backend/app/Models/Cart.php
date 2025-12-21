<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Fields that can be mass assigned (security feature)
    protected $fillable = [
        'user_id',    // User who owns this cart item
        'product_id', // Product in the cart
        'quantity'    // Number of items
    ];

    // Type casting for attributes (automatic data conversion)
    protected $casts = [
        'quantity' => 'integer' // Quantity as integer
    ];

    // Cart belongs to a User (one user can have many cart items)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Cart belongs to a Product (one product can be in many carts)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

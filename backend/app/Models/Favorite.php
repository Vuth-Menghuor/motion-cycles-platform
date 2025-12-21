<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    // Fields that can be mass assigned
    protected $fillable = [
        'user_id',    // ID of the user who favorited the product
        'product_id', // ID of the favorited product
    ];

    // Favorite belongs to a User (each favorite belongs to one user)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Favorite belongs to a Product (each favorite is for one specific product)
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

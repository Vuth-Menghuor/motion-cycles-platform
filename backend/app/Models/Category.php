<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Fields that can be mass assigned
    protected $fillable = [
        'name',        // Category name
        'description'  // Category description
    ];

    // Category has many Products (one category can contain multiple products)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

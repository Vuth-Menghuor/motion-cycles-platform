<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name', 
        'description', 
        'pricing', 
        'category_id', 
        'brand',
        'color',
        'rating',
        'review_count',
        'badge',
        'discount',
        'specs',
        'additional_images',
        'image',
        'quantity',
        'created_at', 
        'updated_at'
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'pricing' => 'decimal:2',
        'badge' => 'array',
        'discount' => 'array',
        'specs' => 'array',
        'additional_images' => 'array',
    ];

    // A product belongs to a category
    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    // A product has many reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

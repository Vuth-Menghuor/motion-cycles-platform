<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['name', 'description', 'pricing', 'category_id', 'created_at', 'updated_at'];

    // A product belongs to a category
    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

}


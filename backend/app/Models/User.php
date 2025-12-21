<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    // The attributes that are mass assignable
    protected $fillable = [
        'username',     // User's username
        'name',         // User's full name
        'email',        // User's email address
        'phone',        // User's phone number
        'password',     // User's password (hashed)
        'provider',     // OAuth provider (google, facebook, etc.)
        'provider_id',  // OAuth provider user ID
        'role'          // User's role (admin, user, etc.)
    ];

    // The attributes that should be hidden for serialization
    protected $hidden = [
        'password',       // Hide password from JSON responses
        'remember_token', // Hide remember token
    ];

    // Get the attributes that should be cast
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Cast to datetime
            'password' => 'hashed',           // Hash passwords automatically
        ];
    }

    // User has many Carts (one user can have multiple shopping cart items)
    public function carts()
    {
        // Step 1: Return hasMany relationship to Cart model
        return $this->hasMany(Cart::class);
    }

    // User has many Favorites (one user can favorite multiple products)
    public function favorites()
    {
        // Step 1: Return hasMany relationship to Favorite model
        return $this->hasMany(Favorite::class);
    }

    // User has many Orders (one user can place multiple orders)
    public function orders()
    {
        // Step 1: Return hasMany relationship to Order model
        return $this->hasMany(Order::class);
    }
}

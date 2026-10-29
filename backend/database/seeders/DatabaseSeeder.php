<?php

namespace Database\Seeders;

use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // The application seeds during deployment. Always ensure the admin
        // account exists, but only add catalogue and sample-order data to an
        // empty database so deploys never duplicate it.
        $this->call(AdminSeeder::class);

        if (! Product::exists()) {
            $this->call([
                CategorySeeder::class,
                ProductSeeder::class,
                OrderSeeder::class,
            ]);
        }
    }
}

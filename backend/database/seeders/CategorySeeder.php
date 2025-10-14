<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Mountain Bike',
            'description' => 'Bikes designed for off-road cycling, featuring robust frames, suspension systems, and tires suitable for rough terrain.',
        ]);

        Category::create([
            'name' => 'Road Bike',
            'description' => 'Bikes optimized for paved roads, emphasizing speed, efficiency, and aerodynamics for long-distance cycling.',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'username' => 'vuthmenghuor',
            'name' => 'Vuth Menghuor',
            'email' => 'vuthmenghuor@example.com',
            'password' => 'password123',
        ]);
    }
}

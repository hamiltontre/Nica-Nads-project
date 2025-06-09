<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    \App\Models\User::create([
        'name' => 'Admin Principal',
        'email' => 'hgtreminio@nads.com',
        'password' => bcrypt('ClaveSegura123'),
        'email_verified_at' => now()
    ]);
}
}

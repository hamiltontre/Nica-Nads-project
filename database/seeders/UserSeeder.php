<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Hamilton Treminio',
            'email' => 'HamiltonTI@nads.com',
            'password' => bcrypt('123'),
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Paola Perez',
            'email' => 'PaolaPZ@nads.com',
            'password' => bcrypt('456'),
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Tymothy Hayes',
            'email' => 'TimmyHY@nads.com',
            'password' => bcrypt('789'),
            'email_verified_at' => now()
        ]);
    }
}
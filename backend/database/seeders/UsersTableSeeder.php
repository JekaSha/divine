<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'id' => 1,
            'name' => 'Jekas',
            'email' => 'shaposhnyk@gmail.com',
            'password' => bcrypt('your_password_here'), // Use a secure password
            'email_verified_at' => null,
        ]);
    }
}

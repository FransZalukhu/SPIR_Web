<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'phone_number' => '08123456789',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);
    }
}

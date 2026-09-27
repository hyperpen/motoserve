<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@motoserve.com'
            ],
            [
                'name' => 'MotoServe Admin',
                'phone' => '09123456789',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }
}
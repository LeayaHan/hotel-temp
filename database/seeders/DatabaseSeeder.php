<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
    ['email' => 'admin@hotel.com'],
    [
        'name'              => 'System Admin',
        'password'          => Hash::make('password'),
        'role'              => 'admin',
        'is_active'         => true,
        'department'        => 'Management',
        'email_verified_at' => now(), 
    ]
);

        // Staff user
        User::firstOrCreate(
            ['email' => 'staff@hotel.com'],
            [
                'name'       => 'Front Desk Staff',
                'password'   => Hash::make('password'),
                'role'       => 'staff',
                'is_active'  => true,
                'department' => 'Front Desk',
            ]
        );

        // Guest user
        User::firstOrCreate(
            ['email' => 'guest@hotel.com'],
            [
                'name'      => 'Sample Guest',
                'password'  => Hash::make('password'),
                'role'      => 'guest',
                'is_active' => true,
            ]
        );
    }
}
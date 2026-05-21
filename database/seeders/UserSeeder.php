<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  public function run(): void
{
    User::firstOrCreate(
        ['email' => 'admin@hotel.com'],
        [
            'name'       => 'Admin User',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'is_active'  => true,
            'department' => null,
        ]
    );

    User::firstOrCreate(
        ['email' => 'frontdesk@hotel.com'],
        [
            'name'       => 'Front Desk Staff',
            'password'   => Hash::make('password'),
            'role'       => 'front_desk',
            'is_active'  => true,
            'department' => 'Front Office',
        ]
    );

    User::firstOrCreate(
        ['email' => 'staff@hotel.com'],
        [
            'name'       => 'Housekeeping Staff',
            'password'   => Hash::make('password'),
            'role'       => 'staff',
            'is_active'  => true,
            'department' => 'Housekeeping',
        ]
    );
}}
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@parking.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Operator user
        User::create([
            'name' => 'Operator',
            'email' => 'operator@parking.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
        ]);

        // Guard user
        User::create([
            'name' => 'Guard',
            'email' => 'guard@parking.com',
            'password' => Hash::make('password'),
            'role' => 'guard',
        ]);
    }
}

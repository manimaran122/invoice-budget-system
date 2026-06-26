<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::admin();
        $userRole = Role::user();

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role_id' => $adminRole?->id,
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'role_id' => $userRole?->id,
                'name' => 'Normal User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}

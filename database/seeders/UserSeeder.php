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
        $adminRole = Role::where('title', 'Admin')->first();
        $userRole = Role::where('title', 'User')->first();

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ],
        );

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
            ],
        );

        if ($adminRole) {
            $admin->roles()->sync([$adminRole->id]);
        }

        if ($userRole) {
            $user->roles()->sync([$userRole->id]);
        }
    }
}

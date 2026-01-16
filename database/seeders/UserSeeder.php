<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::firstOrCreate(
            [
                'email' => 'superadmin@superadmin.com',
            ],
            [
                'name' => 'Super Admin',
                'phone' => '081234567890',
                'password' => bcrypt('password'),
            ],
        );

        // Add role to the superadmin user
        $superadmin->assignRole('superadmin');
        $superadmin->markEmailAsVerified();

        $user = User::firstOrCreate(
            [
                'email' => 'user@user.com',
            ],
            [
                'name' => 'User',
                'phone' => '081234567891',
                'password' => bcrypt('password'),
            ],
        );

        // Add role to the user
        $user->assignRole('user');
    }
}

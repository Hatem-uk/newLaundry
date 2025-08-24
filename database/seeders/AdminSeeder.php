<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'System Admin',
            'email' => 'admin@system.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'approved',
            'coins' => 0 // Admins don't need coins
        ]);

        // Create admin profile
        Admin::create([
            'user_id' => $adminUser->id,
            'name' => 'System Administrator',
            'phone' => '+966500000000',
            'permissions' => ['all'] // Full permissions
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@system.com');
        $this->command->info('Password: password');
    }
}

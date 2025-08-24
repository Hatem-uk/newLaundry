<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'phone' => '+966501234567',
                'password' => Hash::make('secret123'), // Fixed: properly hash password
                'role' => 'admin',
                'status' => 'approved'
            ]
        );

        \info('Admin seeded: '.$admin->email);
    }
}

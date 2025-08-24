<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use App\Models\City;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get cities for distribution
        $cities = City::all();
        
        if ($cities->isEmpty()) {
            $this->command->info('No cities found. Please run CitySeeder first.');
            return;
        }

        // Create customers
        $customerData = [
            ['name' => 'John Smith', 'email' => 'john.smith@example.com', 'city' => 'Riyadh', 'coins' => 1000],
            ['name' => 'Sarah Johnson', 'email' => 'sarah.j@example.com', 'city' => 'Jeddah', 'coins' => 1500],
            ['name' => 'Mike Wilson', 'email' => 'mike.w@example.com', 'city' => 'Dammam', 'coins' => 800],
            ['name' => 'Emily Davis', 'email' => 'emily.d@example.com', 'city' => 'Mecca', 'coins' => 1200],
            ['name' => 'David Brown', 'email' => 'david.b@example.com', 'city' => 'Medina', 'coins' => 900],
            ['name' => 'Lisa Anderson', 'email' => 'lisa.a@example.com', 'city' => 'Abha', 'coins' => 1100],
            ['name' => 'Robert Taylor', 'email' => 'robert.t@example.com', 'city' => 'Tabuk', 'coins' => 700],
        ];
        
        foreach ($customerData as $data) {
            $city = City::where('name', $data['city'])->first();
            if (!$city) continue;
            
            $user = User::factory()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'customer',
                'status' => 'approved'
            ]);
            
            Customer::factory()->create([
                'user_id' => $user->id,
                'city_id' => $city->id,
                'coins' => $data['coins'] // Set coins in customer table too
            ]);
        }

        // Create additional random customers
        $additionalCustomers = rand(3, 7);
        for ($i = 0; $i < $additionalCustomers; $i++) {
            $coins = rand(500, 2000); // Random initial coins
            
            $user = User::factory()->create([
                'role' => 'customer',
                'status' => 'approved'
            ]);
            
            $city = $cities->random();
            
            Customer::factory()->create([
                'user_id' => $user->id,
                'city_id' => $city->id,
                'coins' => $coins
            ]);
        }

        $this->command->info('Customers seeded successfully!');
        $this->command->info('Customers distributed across ' . $cities->count() . ' cities');
    }
}

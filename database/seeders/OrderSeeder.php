<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use App\Models\Customer;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $services = Service::all();
        
        if ($customers->isEmpty() || $services->isEmpty()) {
            $this->command->info('No customers or services found. Please run CustomerSeeder and ServiceSeeder first.');
            return;
        }

        // Create sample orders for services
        foreach ($services as $service) {
            // Create 2-5 orders for each service
            $orderCount = rand(2, 5);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $customer = $customers->random();
                $status = $this->getRandomStatus();
                
                Order::create([
                    'user_id' => $customer->user_id, // Customer who pays
                    'recipient_id' => $customer->user_id, // Customer who receives
                    'provider_id' => $service->provider_id, // Laundry that provides the service
                    'target_id' => $service->id,
                    'target_type' => 'service',
                    'coins' => $service->coin_cost,
                    'price' => $service->price,
                    'status' => $status,
                    'meta' => [
                        'quantity' => rand(1, 3),
                        'notes' => 'طلب تجريبي'
                    ]
                ]);
            }
        }

        $this->command->info('Orders seeded successfully!');
    }

    /**
     * Get random order status
     */
    private function getRandomStatus(): string
    {
        $statuses = ['pending', 'in_process', 'completed', 'canceled'];
        $weights = [30, 20, 40, 10]; // 30% pending, 20% in_process, 40% completed, 10% canceled
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'pending';
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\User;
use App\Models\Laundry;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laundries = Laundry::all();
        
        if ($laundries->isEmpty()) {
            $this->command->error('No laundries found. Please run LaundrySeeder first.');
            return;
        }

        $services = [
            [
                'name' => [
                    'ar' => 'غسيل عادي',
                    'en' => 'Regular Washing'
                ],
                'description' => [
                    'ar' => 'غسيل عادي للملابس اليومية',
                    'en' => 'Regular washing for daily clothes'
                ],
                'coin_cost' => 5,
                'price' => 15.00,
                'quantity' => 100,
                'type' => 'washing',
                'status' => 'approved'
            ],
            [
                'name' => [
                    'ar' => 'غسيل دقيق',
                    'en' => 'Delicate Washing'
                ],
                'description' => [
                    'ar' => 'غسيل دقيق للملابس الحساسة',
                    'en' => 'Delicate washing for sensitive clothes'
                ],
                'coin_cost' => 8,
                'price' => 25.00,
                'quantity' => 50,
                'type' => 'washing',
                'status' => 'approved'
            ],
            [
                'name' => [
                    'ar' => 'كوي عادي',
                    'en' => 'Regular Ironing'
                ],
                'description' => [
                    'ar' => 'كوي عادي للملابس',
                    'en' => 'Regular ironing for clothes'
                ],
                'coin_cost' => 3,
                'price' => 10.00,
                'quantity' => 80,
                'type' => 'ironing',
                'status' => 'approved'
            ],
            [
                'name' => [
                    'ar' => 'كوي دقيق',
                    'en' => 'Delicate Ironing'
                ],
                'description' => [
                    'ar' => 'كوي دقيق للملابس الحساسة',
                    'en' => 'Delicate ironing for sensitive clothes'
                ],
                'coin_cost' => 5,
                'price' => 18.00,
                'quantity' => 40,
                'type' => 'ironing',
                'status' => 'approved'
            ],
            [
                'name' => [
                    'ar' => 'تنظيف جاف',
                    'en' => 'Dry Cleaning'
                ],
                'description' => [
                    'ar' => 'تنظيف جاف للملابس الرسمية',
                    'en' => 'Dry cleaning for formal clothes'
                ],
                'coin_cost' => 12,
                'price' => 35.00,
                'quantity' => 30,
                'type' => 'cleaning',
                'status' => 'approved'
            ],
            [
                'name' => [
                    'ar' => 'إمداد الوكيل',
                    'en' => 'Agent Supply'
                ],
                'description' => [
                    'ar' => 'إمداد الوكيل بالمواد اللازمة',
                    'en' => 'Agent supply with necessary materials'
                ],
                'coin_cost' => 20,
                'price' => 60.00,
                'quantity' => 20,
                'type' => 'agent_supply',
                'status' => 'approved'
            ]
        ];

        foreach ($services as $serviceData) {
            foreach ($laundries as $laundry) {
                Service::create([
                    'provider_id' => $laundry->user_id,
                    'name' => $serviceData['name'],
                    'description' => $serviceData['description'],
                    'coin_cost' => $serviceData['coin_cost'],
                    'price' => $serviceData['price'],
                    'quantity' => $serviceData['quantity'],
                    'type' => $serviceData['type'],
                    'status' => $serviceData['status']
                ]);
            }
        }

        // Create additional random services
        Service::factory()->count(20)->create([
            'provider_id' => function() use ($laundries) {
                return $laundries->random()->user_id;
            }
        ]);
    }
}


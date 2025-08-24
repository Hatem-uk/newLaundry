<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laundry;
use App\Models\User;
use App\Models\City;

class LaundrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all();
        
        if ($cities->isEmpty()) {
            $this->command->error('No cities found. Please run CitySeeder first.');
            return;
        }

        // Create laundry users and profiles
        $laundryData = [
            [
                'user' => [
                    'name' => 'Laundry User 1',
                    'email' => 'laundry1@example.com',
                    'password' => bcrypt('123456'),
                    'role' => 'laundry',
                    'phone' => '0500000001',
                    'status' => 'approved'
                ],
                'laundry' => [
                    'name' => [
                        'ar' => 'مغسلة النظافة',
                        'en' => 'Clean Laundry'
                    ],
                    'address' => [
                        'ar' => 'شارع الملك فهد، الرياض',
                        'en' => 'King Fahd Street, Riyadh'
                    ],
                    'phone' => '0110000001',
                    'status' => 'online',
                    'working_hours' => [
                        'monday' => ['08:00', '18:00'],
                        'tuesday' => ['08:00', '18:00'],
                        'wednesday' => ['08:00', '18:00'],
                        'thursday' => ['08:00', '18:00'],
                        'friday' => ['09:00', '16:00'],
                        'saturday' => ['08:00', '18:00'],
                        'sunday' => ['08:00', '18:00']
                    ],
                    'delivery_available' => true,
                    'pickup_available' => true
                ]
            ],
            [
                'user' => [
                    'name' => 'Laundry User 2',
                    'email' => 'laundry2@example.com',
                    'password' => bcrypt('123456'),
                    'role' => 'laundry',
                    'phone' => '0500000002',
                    'status' => 'approved'
                ],
                'laundry' => [
                    'name' => [
                        'ar' => 'مغسلة الأمانة',
                        'en' => 'Trust Laundry'
                    ],
                    'address' => [
                        'ar' => 'شارع التحلية، جدة',
                        'en' => 'Tahlia Street, Jeddah'
                    ],
                    'phone' => '0120000001',
                    'status' => 'online',
                    'working_hours' => [
                        'monday' => ['07:00', '19:00'],
                        'tuesday' => ['07:00', '19:00'],
                        'wednesday' => ['07:00', '19:00'],
                        'thursday' => ['07:00', '19:00'],
                        'friday' => ['08:00', '17:00'],
                        'saturday' => ['07:00', '19:00'],
                        'sunday' => ['07:00', '19:00']
                    ],
                    'delivery_available' => true,
                    'pickup_available' => true
                ]
            ],
            [
                'user' => [
                    'name' => 'Laundry User 3',
                    'email' => 'laundry3@example.com',
                    'password' => bcrypt('123456'),
                    'role' => 'laundry',
                    'phone' => '0500000003',
                    'status' => 'approved'
                ],
                'laundry' => [
                    'name' => [
                        'ar' => 'مغسلة النجوم',
                        'en' => 'Stars Laundry'
                    ],
                    'address' => [
                        'ar' => 'شارع الملك خالد، الدمام',
                        'en' => 'King Khalid Street, Dammam'
                    ],
                    'phone' => '0130000001',
                    'status' => 'online',
                    'working_hours' => [
                        'monday' => ['08:00', '20:00'],
                        'tuesday' => ['08:00', '20:00'],
                        'wednesday' => ['08:00', '20:00'],
                        'thursday' => ['08:00', '20:00'],
                        'friday' => ['09:00', '18:00'],
                        'saturday' => ['08:00', '20:00'],
                        'sunday' => ['08:00', '20:00']
                    ],
                    'delivery_available' => false,
                    'pickup_available' => true
                ]
            ]
        ];

        foreach ($laundryData as $data) {
            $user = User::create($data['user']);
            
            $laundryData = $data['laundry'];
            $laundryData['user_id'] = $user->id;
            $laundryData['city_id'] = $cities->random()->id;
            
            Laundry::create($laundryData);
        }

        // Create additional random laundries
        Laundry::factory()->count(7)->create([
            'city_id' => function() use ($cities) {
                return $cities->random()->id;
            }
        ]);
    }
}

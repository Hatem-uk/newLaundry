<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'name' => [
                    'ar' => 'الرياض',
                    'en' => 'Riyadh'
                ],
                'region' => 'Riyadh',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'is_active' => true
            ],
            [
                'name' => [
                    'ar' => 'جدة',
                    'en' => 'Jeddah'
                ],
                'region' => 'Makkah',
                'latitude' => 21.4858,
                'longitude' => 39.1925,
                'is_active' => true
            ],
            [
                'name' => [
                    'ar' => 'الدمام',
                    'en' => 'Dammam'
                ],
                'region' => 'Eastern Province',
                'latitude' => 26.4207,
                'longitude' => 50.0888,
                'is_active' => true
            ],
            [
                'name' => [
                    'ar' => 'مكة المكرمة',
                    'en' => 'Makkah'
                ],
                'region' => 'Makkah',
                'latitude' => 21.3891,
                'longitude' => 39.8579,
                'is_active' => true
            ],
            [
                'name' => [
                    'ar' => 'المدينة المنورة',
                    'en' => 'Medina'
                ],
                'region' => 'Medina',
                'latitude' => 24.5247,
                'longitude' => 39.5692,
                'is_active' => true
            ],
            [
                'name' => [
                    'ar' => 'الطائف',
                    'en' => 'Taif'
                ],
                'region' => 'Makkah',
                'latitude' => 21.2703,
                'longitude' => 40.4158,
                'is_active' => true
            ],
            [
                'name' => [
                    'ar' => 'تبوك',
                    'en' => 'Tabuk'
                ],
                'region' => 'Tabuk',
                'latitude' => 28.3835,
                'longitude' => 36.5664,
                'is_active' => true
            ],
            [
                'name' => [
                    'ar' => 'أبها',
                    'en' => 'Abha'
                ],
                'region' => 'Asir',
                'latitude' => 18.2164,
                'longitude' => 42.5053,
                'is_active' => true
            ],
            [
                'name' => [
                    'ar' => 'حائل',
                    'en' => 'Hail'
                ],
                'region' => 'Hail',
                'latitude' => 27.5111,
                'longitude' => 41.7208,
                'is_active' => true
            ],
            [
                'name' => [
                    'ar' => 'بريدة',
                    'en' => 'Buraidah'
                ],
                'region' => 'Qassim',
                'latitude' => 26.3360,
                'longitude' => 43.9632,
                'is_active' => true
            ]
        ];

        foreach ($cities as $cityData) {
            City::create($cityData);
        }
    }
}

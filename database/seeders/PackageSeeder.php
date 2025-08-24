<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => [
                    'ar' => 'حزمة البداية',
                    'en' => 'Starter Package'
                ],
                'description' => [
                    'ar' => 'حزمة مثالية للمبتدئين',
                    'en' => 'Perfect package for beginners'
                ],
                'price' => 50.00,
                'type' => 'starter',
                'coins_amount' => 100,
                'status' => 'active'
            ],
            [
                'name' => [
                    'ar' => 'الحزمة المتوسطة',
                    'en' => 'Standard Package'
                ],
                'description' => [
                    'ar' => 'حزمة متوسطة للاستخدام العادي',
                    'en' => 'Standard package for regular use'
                ],
                'price' => 100.00,
                'type' => 'standard',
                'coins_amount' => 250,
                'status' => 'active'
            ],
            [
                'name' => [
                    'ar' => 'الحزمة المميزة',
                    'en' => 'Premium Package'
                ],
                'description' => [
                    'ar' => 'حزمة مميزة للاستخدام المكثف',
                    'en' => 'Premium package for intensive use'
                ],
                'price' => 200.00,
                'type' => 'premium',
                'coins_amount' => 600,
                'status' => 'active'
            ],
            [
                'name' => [
                    'ar' => 'الحزمة الاحترافية',
                    'en' => 'Professional Package'
                ],
                'description' => [
                    'ar' => 'حزمة احترافية للاستخدام التجاري',
                    'en' => 'Professional package for commercial use'
                ],
                'price' => 500.00,
                'type' => 'professional',
                'coins_amount' => 1500,
                'status' => 'active'
            ],
            [
                'name' => [
                    'ar' => 'حزمة الهدية',
                    'en' => 'Gift Package'
                ],
                'description' => [
                    'ar' => 'حزمة هدايا مثالية للأصدقاء والعائلة',
                    'en' => 'Perfect gift package for friends and family'
                ],
                'price' => 75.00,
                'type' => 'gift',
                'coins_amount' => 150,
                'status' => 'active'
            ]
        ];

        foreach ($packages as $packageData) {
            Package::create($packageData);
        }

        // Create additional random packages
        Package::factory()->count(5)->create();
    }
}

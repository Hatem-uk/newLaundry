<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CitySeeder::class,        // First cities
            AdminUserSeeder::class,   // Then admin user
            CustomerSeeder::class,    // Then customers
            LaundrySeeder::class,     // Then laundries
            ServiceSeeder::class,     // Then services
            PackageSeeder::class,     // Then coin packages
            OrderSeeder::class,       // Then orders (depends on customers, services)
            RatingSeeder::class,      // Then ratings (depends on customers, laundries, orders)
        ]);
    }
}

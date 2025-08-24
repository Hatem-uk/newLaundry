<?php

namespace Database\Factories;

use App\Models\Laundry;
use App\Models\User;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Laundry>
 */
class LaundryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = City::inRandomOrder()->first() ?? City::factory()->create();
        
        return [
            'user_id' => User::factory()->state(['role' => 'laundry']),
            'name' => fake()->company() . ' Laundry',
            'logo' => null,
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'city_id' => $city->id,
            'status' => fake()->randomElement(['online', 'offline', 'maintenance']),
            'is_active' => true,
            'working_hours' => [
                'monday' => ['08:00', '18:00'],
                'tuesday' => ['08:00', '18:00'],
                'wednesday' => ['08:00', '18:00'],
                'thursday' => ['08:00', '18:00'],
                'friday' => ['09:00', '16:00'],
                'saturday' => ['08:00', '18:00'],
                'sunday' => ['08:00', '18:00']
            ],
            'delivery_available' => fake()->boolean(70),
            'pickup_available' => true
        ];
    }

    /**
     * Indicate that the laundry is online.
     */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'online',
        ]);
    }

    /**
     * Indicate that the laundry is offline.
     */
    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'offline',
        ]);
    }

    /**
     * Indicate that the laundry is in maintenance.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }

    /**
     * Indicate that the laundry has a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Indicate that the laundry is in a specific city.
     */
    public function inCity(City $city): static
    {
        return $this->state(fn (array $attributes) => [
            'city_id' => $city->id,
        ]);
    }

    /**
     * Indicate that the laundry offers delivery.
     */
    public function withDelivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'delivery_available' => true,
        ]);
    }
}

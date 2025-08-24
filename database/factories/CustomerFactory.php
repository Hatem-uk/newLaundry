<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
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
            'user_id' => User::factory()->state(['role' => 'customer']),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'image' => null,
            'city_id' => $city->id,
            'coins' => fake()->numberBetween(0, 1000) // Random coin balance
        ];
    }

    /**
     * Indicate that the customer is in a specific city.
     */
    public function inCity(City $city): static
    {
        return $this->state(fn (array $attributes) => [
            'city_id' => $city->id,
        ]);
    }

    /**
     * Indicate that the customer has a specific coin balance.
     */
    public function withCoins(int $coins): static
    {
        return $this->state(fn (array $attributes) => [
            'coins' => $coins,
        ]);
    }

    /**
     * Indicate that the customer has no coins.
     */
    public function noCoins(): static
    {
        return $this->state(fn (array $attributes) => [
            'coins' => 0,
        ]);
    }
}


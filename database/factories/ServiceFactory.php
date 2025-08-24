<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => User::factory()->state(['role' => 'laundry']),
            'name' => [
                'en' => fake()->words(3, true),
                'ar' => fake()->words(3, true)
            ],
            'description' => [
                'en' => fake()->paragraph(),
                'ar' => fake()->paragraph()
            ],
            'price' => fake()->randomFloat(2, 10, 1000),
            'coin_cost' => fake()->numberBetween(10, 100),
            'quantity' => fake()->numberBetween(1, 10),
            'type' => fake()->randomElement(['washing', 'ironing', 'dry_cleaning', 'agent_supply']),
            'image' => null,
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
        ];
    }

    /**
     * Indicate that the service is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the service is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the service is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}



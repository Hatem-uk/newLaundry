<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agent>
 */
class AgentFactory extends Factory
{
    protected $model = Agent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'agent']),
            'specialization' => fake()->randomElement(['Sales', 'Support', 'Technical', 'Consulting']),
            'experience_years' => fake()->numberBetween(1, 15),
            'commission_rate' => fake()->randomFloat(2, 5, 25),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'image' => null,
        ];
    }

    /**
     * Indicate that the agent is experienced.
     */
    public function experienced(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_years' => fake()->numberBetween(10, 20),
        ]);
    }

    /**
     * Indicate that the agent is new.
     */
    public function new(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_years' => fake()->numberBetween(1, 3),
        ]);
    }
}


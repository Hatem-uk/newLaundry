<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetType = $this->faker->randomElement([Package::class, Service::class]);
        $status = $this->faker->randomElement(['pending', 'in_process', 'completed', 'canceled']);
        
        return [
            'user_id' => User::factory(),
            'recipient_id' => User::factory(),
            'provider_id' => User::factory(),
            'target_id' => $targetType === Package::class ? Package::factory() : Service::factory(),
            'target_type' => $targetType,
            'coins' => $this->faker->numberBetween(-1000, 1000),
            'price' => $this->faker->randomFloat(2, 0, 500),
            'status' => $status,
            'meta' => [
                'quantity' => $this->faker->numberBetween(1, 5),
                'notes' => $this->faker->sentence()
            ]
        ];
    }

    /**
     * Indicate that the order is for a package.
     */
    public function package(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_type' => Package::class,
            'target_id' => Package::factory(),
            'coins' => $this->faker->numberBetween(100, 10000),
            'price' => $this->faker->randomFloat(2, 25, 500),
        ]);
    }

    /**
     * Indicate that the order is for a service.
     */
    public function service(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_type' => Service::class,
            'target_id' => Service::factory(),
            'coins' => $this->faker->numberBetween(-1000, 0),
            'price' => 0,
        ]);
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the order is in process.
     */
    public function inProcess(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_process',
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the order is canceled.
     */
    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'canceled',
        ]);
    }

    /**
     * Indicate that the order involves coins.
     */
    public function withCoins(): static
    {
        return $this->state(fn (array $attributes) => [
            'coins' => $this->faker->numberBetween(100, 10000),
        ]);
    }

    /**
     * Indicate that the order involves cash.
     */
    public function withCash(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->randomFloat(2, 10, 500),
            'coins' => 0,
        ]);
    }
}

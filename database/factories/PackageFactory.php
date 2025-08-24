<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['starter', 'premium', 'bulk', 'special'];
        $type = $this->faker->randomElement($types);
        
        // Generate appropriate price and coin amounts based on type
        $price = match($type) {
            'starter' => $this->faker->randomFloat(2, 25, 75),
            'premium' => $this->faker->randomFloat(2, 75, 150),
            'bulk' => $this->faker->randomFloat(2, 150, 300),
            'special' => $this->faker->randomFloat(2, 50, 100),
            default => $this->faker->randomFloat(2, 50, 200)
        };
        
        // Generate coin amounts (roughly 100 coins per SAR)
        $coinsAmount = (int)($price * 100);
        
        return [
            'name' => $this->faker->words(2, true),
            'price' => $price,
            'type' => $type,
            'coins_amount' => $coinsAmount,
            'status' => $this->faker->randomElement(['active', 'inactive'])
        ];
    }

    /**
     * Indicate that the package is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the package is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Create a starter package.
     */
    public function starter(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'starter',
            'price' => $this->faker->randomFloat(2, 25, 75),
        ]);
    }

    /**
     * Create a premium package.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'premium',
            'price' => $this->faker->randomFloat(2, 75, 150),
        ]);
    }

    /**
     * Create a bulk package.
     */
    public function bulk(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'bulk',
            'price' => $this->faker->randomFloat(2, 150, 300),
        ]);
    }

    /**
     * Create a special package.
     */
    public function special(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'special',
            'price' => $this->faker->randomFloat(2, 50, 100),
        ]);
    }
}

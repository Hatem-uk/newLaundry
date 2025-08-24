<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentMethod = $this->faker->randomElement(['cash', 'online', 'coins']);
        $status = $this->faker->randomElement(['pending', 'paid', 'refunded']);
        
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'payer_id' => User::factory(),
            'provider_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'payment_method' => $paymentMethod,
            'status' => $status,
            'meta' => [
                'notes' => $this->faker->sentence(),
                'reference' => $this->faker->uuid()
            ]
        ];
    }

    /**
     * Indicate that the invoice is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the invoice is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    /**
     * Indicate that the invoice is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
        ]);
    }

    /**
     * Indicate that the payment method is cash.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cash',
        ]);
    }

    /**
     * Indicate that the payment method is online.
     */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'online',
        ]);
    }

    /**
     * Indicate that the payment method is coins.
     */
    public function coins(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'coins',
        ]);
    }
}

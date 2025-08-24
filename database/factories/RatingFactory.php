<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\Customer;
use App\Models\Laundry;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first() ?? Customer::factory()->create();
        $laundry = Laundry::inRandomOrder()->first() ?? Laundry::factory()->create();
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();

        $serviceTypes = ['washing', 'ironing', 'cleaning', 'agent_supply', 'other'];

        return [
            'customer_id' => $customer->id,
            'laundry_id' => $laundry->id,
            'order_id' => $order->id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional(0.7)->sentence(10),
            'service_type' => fake()->randomElement($serviceTypes),
        ];
    }

    /**
     * تقييم إيجابي (4-5 نجوم)
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(4, 5),
            'comment' => fake()->sentence(10),
        ]);
    }

    /**
     * تقييم متوسط (3 نجوم)
     */
    public function average(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 3,
            'comment' => fake()->sentence(8),
        ]);
    }

    /**
     * تقييم سلبي (1-2 نجوم)
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(1, 2),
            'comment' => fake()->sentence(12),
        ]);
    }

    /**
     * تقييم لمغسلة محددة
     */
    public function forLaundry($laundryId): static
    {
        return $this->state(fn (array $attributes) => [
            'laundry_id' => $laundryId,
        ]);
    }

    /**
     * تقييم من عميل محدد
     */
    public function fromCustomer($customerId): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id' => $customerId,
        ]);
    }

    /**
     * تقييم لطلب محدد
     */
    public function forOrder($orderId): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $orderId,
        ]);
    }
}

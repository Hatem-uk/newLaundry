<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Major Saudi cities with their regions
        $saudiCities = [
            'Riyadh' => ['name' => 'Riyadh', 'name_ar' => 'الرياض', 'region' => 'Riyadh Region'],
            'Jeddah' => ['name' => 'Jeddah', 'name_ar' => 'جدة', 'region' => 'Makkah Region'],
            'Dammam' => ['name' => 'Dammam', 'name_ar' => 'الدمام', 'region' => 'Eastern Province'],
            'Mecca' => ['name' => 'Mecca', 'name_ar' => 'مكة المكرمة', 'region' => 'Makkah Region'],
            'Medina' => ['name' => 'Medina', 'name_ar' => 'المدينة المنورة', 'region' => 'Al Madinah Region'],
            'Abha' => ['name' => 'Abha', 'name_ar' => 'أبها', 'region' => 'Asir Region'],
            'Tabuk' => ['name' => 'Tabuk', 'name_ar' => 'تبوك', 'region' => 'Tabuk Region'],
            'Hail' => ['name' => 'Hail', 'name_ar' => 'حائل', 'region' => 'Hail Region'],
            'Buraidah' => ['name' => 'Buraidah', 'name_ar' => 'بريدة', 'region' => 'Al Qassim Region'],
            'Khamis Mushait' => ['name' => 'Khamis Mushait', 'name_ar' => 'خميس مشيط', 'region' => 'Asir Region'],
            'Al Khobar' => ['name' => 'Al Khobar', 'name_ar' => 'الخبر', 'region' => 'Eastern Province'],
            'Al Jubail' => ['name' => 'Al Jubail', 'name_ar' => 'الجبيل', 'region' => 'Eastern Province'],
            'Al Ahsa' => ['name' => 'Al Ahsa', 'name_ar' => 'الأحساء', 'region' => 'Eastern Province'],
            'Al Kharj' => ['name' => 'Al Kharj', 'name_ar' => 'الخرج', 'region' => 'Riyadh Region'],
            'Al Qatif' => ['name' => 'Al Qatif', 'name_ar' => 'القطيف', 'region' => 'Eastern Province'],
            'Yanbu' => ['name' => 'Yanbu', 'name_ar' => 'ينبع', 'region' => 'Al Madinah Region'],
            'Najran' => ['name' => 'Najran', 'name_ar' => 'نجران', 'region' => 'Najran Region'],
            'Jizan' => ['name' => 'Jizan', 'name_ar' => 'جازان', 'region' => 'Jizan Region'],
            'Al Bahah' => ['name' => 'Al Bahah', 'name_ar' => 'الباحة', 'region' => 'Al Bahah Region'],
            'Arar' => ['name' => 'Arar', 'name_ar' => 'عرعر', 'region' => 'Northern Borders Region']
        ];

        $city = $this->faker->randomElement(array_values($saudiCities));

        return [
            'name' => $city['name'],
            'name_ar' => $city['name_ar'],
            'region' => $city['region'],
            'is_active' => true,
            'latitude' => $this->faker->latitude(16, 32), // Saudi Arabia latitude range
            'longitude' => $this->faker->longitude(34, 56), // Saudi Arabia longitude range
        ];
    }

    /**
     * Indicate that the city is in a specific region.
     */
    public function inRegion(string $region): static
    {
        return $this->state(fn (array $attributes) => [
            'region' => $region,
        ]);
    }

    /**
     * Indicate that the city is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

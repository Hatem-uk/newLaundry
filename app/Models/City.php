<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'region',
        'latitude',
        'longitude',
        'is_active'
    ];

    public $translatable = ['name'];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean'
    ];

    /**
     * Get all laundries in this city
     */
    public function laundries(): HasMany
    {
        return $this->hasMany(Laundry::class);
    }

    /**
     * Get all agents in this city
     */
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    /**
     * Get all customers in this city
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Scope to get only active cities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get cities by region
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Calculate distance between two cities using Haversine formula
     */
    public function distanceTo(City $otherCity): float
    {
        if (!$this->latitude || !$this->longitude || !$otherCity->latitude || !$otherCity->longitude) {
            return 0;
        }

        $lat1 = deg2rad($this->latitude);
        $lon1 = deg2rad($this->longitude);
        $lat2 = deg2rad($otherCity->latitude);
        $lon2 = deg2rad($otherCity->longitude);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Earth radius in kilometers
        $radius = 6371;

        return $radius * $c;
    }

    /**
     * Get nearby cities within specified radius (in km)
     */
    public function getNearbyCities(float $radius = 50): \Illuminate\Database\Eloquent\Collection
    {
        return City::where('id', '!=', $this->id)
            ->where('is_active', true)
            ->get()
            ->filter(function ($city) use ($radius) {
                return $this->distanceTo($city) <= $radius;
            })
            ->sortBy(function ($city) {
                return $this->distanceTo($city);
            })
            ->values();
    }

    /**
     * Get nearby cities with distance information
     */
    public function getNearbyCitiesWithDistance(float $radius = 50): \Illuminate\Support\Collection
    {
        return City::where('id', '!=', $this->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($city) {
                return [
                    'city' => $city,
                    'distance' => $this->distanceTo($city)
                ];
            })
            ->filter(function ($item) use ($radius) {
                return $item['distance'] <= $radius;
            })
            ->sortBy('distance')
            ->values();
    }
}

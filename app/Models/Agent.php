<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Agent extends Model
{
    use HasFactory, HasTranslations;
    
    protected $fillable = [
        'user_id',
        'name', 
        'license_number', 
        'phone', 
        'address',
        'logo',
        'city_id',
        'status', // online, offline, maintenance
        'is_active',
        'working_hours',
        'service_areas', // JSON array of city IDs they serve
        'specializations' // JSON array of service types
    ];

    public $translatable = ['name', 'address'];

    protected $casts = [
        'is_active' => 'boolean',
        'working_hours' => 'array',
        'service_areas' => 'array',
        'specializations' => 'array'
    ];

    // Status constants
    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';
    const STATUS_MAINTENANCE = 'maintenance';

    public function user() { 
        return $this->belongsTo(User::class); 
    }

    public function city() { 
        return $this->belongsTo(City::class); 
    }

    public function services() { 
        return $this->hasMany(Service::class, 'provider_id', 'user_id'); 
    }

    /**
     * Scope to get only online agents
     */
    public function scopeOnline($query)
    {
        return $query->where('status', self::STATUS_ONLINE);
    }

    /**
     * Scope to get only active agents
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get agents by city
     */
    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope to get agents by region
     */
    public function scopeByRegion($query, $region)
    {
        return $query->whereHas('city', function($q) use ($region) {
            $q->where('region', $region);
        });
    }

    /**
     * Check if agent is online
     */
    public function isOnline(): bool
    {
        return $this->status === self::STATUS_ONLINE;
    }

    /**
     * Check if agent is offline
     */
    public function isOffline(): bool
    {
        return $this->status === self::STATUS_OFFLINE;
    }

    /**
     * Check if agent is in maintenance
     */
    public function isInMaintenance(): bool
    {
        return $this->status === self::STATUS_MAINTENANCE;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ONLINE => 'Online',
            self::STATUS_OFFLINE => 'Offline',
            self::STATUS_MAINTENANCE => 'Maintenance',
            default => 'Unknown'
        };
    }

    /**
     * Check if agent serves a specific city
     */
    public function servesCity($cityId): bool
    {
        return in_array($cityId, $this->service_areas ?? []);
    }

    /**
     * Get distance to a specific city
     */
    public function getDistanceToCity(City $city): float
    {
        return $this->city->distanceTo($city);
    }

    /**
     * Scope to get agents in same city
     */
    public function scopeInSameCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope to get agents in nearby cities
     */
    public function scopeInNearbyCities($query, City $city, $radius = 50)
    {
        $nearbyCityIds = $city->getNearbyCities($radius)->pluck('id');
        return $query->whereIn('city_id', $nearbyCityIds);
    }

    /**
     * Get agents with distance information from a specific city
     */
    public static function getNearbyAgents(City $city, $radius = 50)
    {
        $nearbyCities = $city->getNearbyCitiesWithDistance($radius);
        
        $agents = collect();
        
        foreach ($nearbyCities as $nearbyCity) {
            $cityAgents = self::where('city_id', $nearbyCity['city']->id)
                ->online()
                ->active()
                ->with(['city', 'user'])
                ->get()
                ->map(function ($agent) use ($nearbyCity) {
                    $agent->distance_from_customer = $nearbyCity['distance'];
                    return $agent;
                });
            
            $agents = $agents->merge($cityAgents);
        }

        // Add same city agents
        $sameCityAgents = self::where('city_id', $city->id)
            ->online()
            ->active()
            ->with(['city', 'user'])
            ->get()
            ->map(function ($agent) {
                $agent->distance_from_customer = 0;
                return $agent;
            });

        $agents = $agents->merge($sameCityAgents);

        return $agents->sortBy('distance_from_customer')->values();
    }

    /**
     * Get agents that serve a specific city (including nearby cities)
     */
    public static function getAgentsForCity(City $city, $radius = 50)
    {
        $nearbyCities = $city->getNearbyCitiesWithDistance($radius);
        $allCityIds = $nearbyCities->pluck('city.id')->push($city->id);
        
        return self::whereIn('city_id', $allCityIds)
            ->where(function($query) use ($city, $allCityIds) {
                $query->whereIn('city_id', $allCityIds)
                      ->orWhereJsonContains('service_areas', $city->id)
                      ->orWhereJsonContains('service_areas', $allCityIds->toArray());
            })
            ->online()
            ->active()
            ->with(['city', 'user'])
            ->get()
            ->map(function ($agent) use ($city) {
                $agent->distance_from_customer = $agent->getDistanceToCity($city);
                return $agent;
            })
            ->sortBy('distance_from_customer')
            ->values();
    }
}

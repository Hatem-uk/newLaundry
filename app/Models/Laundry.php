<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Laundry extends Model
{
    use HasFactory, HasTranslations;
    
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'logo',
        'city_id',
        'status', // online, offline, maintenance
        'is_active',
        'working_hours',
        'delivery_available',
        'pickup_available',
        'service_areas',
        'specializations',
        'description',
        'delivery_radius',
        'website',
        'facebook',
        'instagram',
        'whatsapp',
        'latitude',
        'longitude'
    ];

    public $translatable = ['name', 'address'];

    protected $casts = [
        'is_active' => 'boolean',
        'delivery_available' => 'boolean',
        'pickup_available' => 'boolean',
        'working_hours' => 'array',
        'service_areas' => 'array',
        'specializations' => 'array',
        'delivery_radius' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    // Status constants
    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';
    const STATUS_MAINTENANCE = 'maintenance';

    public function user() { return $this->belongsTo(User::class); }
    public function city() { return $this->belongsTo(City::class); }
    public function workers() { return $this->hasMany(Worker::class); }
    public function services() { return $this->hasMany(Service::class, 'provider_id', 'user_id'); }
    public function ratings() { return $this->hasMany(Rating::class); }
    public function orders() { return $this->hasMany(Order::class, 'provider_id', 'user_id'); }

    /**
     * Scope to get only online laundries
     */
    public function scopeOnline($query)
    {
        return $query->where('status', self::STATUS_ONLINE);
    }

    /**
     * Scope to get only active laundries
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get laundries by city
     */
    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope to get laundries by region
     */
    public function scopeByRegion($query, $region)
    {
        return $query->whereHas('city', function($q) use ($region) {
            $q->where('region', $region);
        });
    }

    /**
     * Check if laundry is online
     */
    public function isOnline(): bool
    {
        return $this->status === self::STATUS_ONLINE;
    }

    /**
     * Check if laundry is offline
     */
    public function isOffline(): bool
    {
        return $this->status === self::STATUS_OFFLINE;
    }

    /**
     * Check if laundry is in maintenance
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
     * Get distance to a specific city
     */
    public function getDistanceToCity(City $city): float
    {
        return $this->city->distanceTo($city);
    }

    /**
     * Scope to get laundries in same city
     */
    public function scopeInSameCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope to get laundries in nearby cities
     */
    public function scopeInNearbyCities($query, City $city, $radius = 50)
    {
        $nearbyCityIds = $city->getNearbyCities($radius)->pluck('id');
        return $query->whereIn('city_id', $nearbyCityIds);
    }

    /**
     * Get laundries with distance information from a specific city
     */
    public static function getNearbyLaundries(City $city, $radius = 50)
    {
        $nearbyCities = $city->getNearbyCitiesWithDistance($radius);
        
        $laundries = collect();
        
        foreach ($nearbyCities as $nearbyCity) {
            $cityLaundries = self::where('city_id', $nearbyCity['city']->id)
                ->online()
                ->active()
                ->with(['city', 'services' => function($q) {
                    $q->where('status', 'approved');
                }])
                ->get()
                ->map(function ($laundry) use ($nearbyCity) {
                    $laundry->distance_from_customer = $nearbyCity['distance'];
                    return $laundry;
                });
            
            $laundries = $laundries->merge($cityLaundries);
        }

        // Add same city laundries
        $sameCityLaundries = self::where('city_id', $city->id)
            ->online()
            ->active()
            ->with(['city', 'services' => function($q) {
                $q->where('status', 'approved');
            }])
            ->get()
            ->map(function ($laundry) {
                $laundry->distance_from_customer = 0;
                return $laundry;
            });

        $laundries = $laundries->merge($sameCityLaundries);

        return $laundries->sortBy('distance_from_customer')->values();
    }

    /**
     * Get average rating for the laundry
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->ratings()->where('rating', '>', 0)->avg('rating') ?? 0.0;
    }

    /**
     * Get total rating count for the laundry
     */
    public function getRatingCountAttribute(): int
    {
        return $this->ratings()->where('rating', '>', 0)->count();
    }

    /**
     * Get rating distribution for the laundry
     */
    public function getRatingDistributionAttribute(): array
    {
        return $this->ratings()
            ->where('rating', '>', 0)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();
    }

    /**
     * Get recent ratings for the laundry
     */
    public function getRecentRatingsAttribute($limit = 5)
    {
        return $this->ratings()
            ->with('customer.user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if laundry has any ratings
     */
    public function hasRatings(): bool
    {
        return $this->ratings()->where('rating', '>', 0)->exists();
    }

    /**
     * Get rating percentage for a specific rating value
     */
    public function getRatingPercentage($rating): float
    {
        $totalRatings = $this->rating_count;
        if ($totalRatings === 0) return 0.0;
        
        $ratingCount = $this->ratings()->where('rating', $rating)->count();
        return round(($ratingCount / $totalRatings) * 100, 1);
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Customer extends Model
{
    use HasFactory, HasTranslations;
    
    protected $fillable = [
        'user_id', 
        'address', 
        'phone', 
        'image',
        'city_id',
        'coins',
        'preferences',
        'meta'
    ];

    public $translatable = ['address'];

    protected $casts = [
        'preferences' => 'array',
        'meta' => 'array'
    ];
    
    public function user() { return $this->belongsTo(User::class); }
    public function city() { return $this->belongsTo(City::class); }
    public function ratings() { return $this->hasMany(Rating::class); }
    
    // New relationships for the coin economy
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function receivedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'recipient_id', 'user_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'user_id', 'user_id');
    }

    /**
     * Scope to get customers by city
     */
    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope to get customers by region
     */
    public function scopeByRegion($query, $region)
    {
        return $query->whereHas('city', function($q) use ($region) {
            $q->where('region', $region);
        });
    }

    /**
     * Add coins to customer's balance
     */
    public function addCoins(int $amount): void
    {
        $this->increment('coins', $amount);
    }

    /**
     * Deduct coins from customer's balance
     */
    public function deductCoins(int $amount): bool
    {
        if ($this->coins >= $amount) {
            $this->decrement('coins', $amount);
            return true;
        }
        return false;
    }

    /**
     * Check if customer has enough coins
     */
    public function hasEnoughCoins(int $amount): bool
    {
        return $this->coins >= $amount;
    }

    /**
     * Get formatted coin balance
     */
    public function getFormattedCoinsAttribute(): string
    {
        return number_format($this->coins) . ' coins';
    }

    /**
     * Get coin balance in SAR (approximate)
     */
    public function getCoinsInSarAttribute(): float
    {
        return $this->coins * 0.01; // Assuming 1 coin = 0.01 SAR
    }
}

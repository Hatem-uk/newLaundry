<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Service statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Service types
     */
    const TYPE_WASHING = 'washing';
    const TYPE_IRONING = 'ironing';
    const TYPE_DRY_CLEANING = 'dry_cleaning';
    const TYPE_AGENT_SUPPLY = 'agent_supply';
    const TYPE_LAUNDRY_SERVICE = 'laundry_service';

    protected $fillable = [
        'provider_id',
        'name',
        'description',
        'coin_cost',
        'price',
        'quantity',
        'type',
        'image',
        'status',
        'meta'
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'coin_cost' => 'integer',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'meta' => 'array'
    ];

    /**
     * Get the provider (laundry or agent) that owns the service.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Get the laundry provider (if provider is a laundry)
     */
    public function laundry(): BelongsTo
    {
        return $this->belongsTo(Laundry::class, 'provider_id', 'user_id');
    }

    /**
     * Get the agent provider (if provider is an agent)
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'provider_id', 'user_id');
    }

    /**
     * Get the orders for this service.
     */
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'target');
    }

    /**
     * Scope a query to only include pending services.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include approved services.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected services.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include services by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include services by provider.
     */
    public function scopeByProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    /**
     * Check if service is active
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_APPROVED]);
    }

    /**
     * Check if service can be purchased with coins
     */
    public function canBePurchasedWithCoins(): bool
    {
        return $this->coin_cost !== null && $this->coin_cost > 0;
    }

    /**
     * Check if service can be purchased with cash
     */
    public function canBePurchasedWithCash(): bool
    {
        return $this->price !== null && $this->price > 0;
    }

    /**
     * Get the cost for a specific quantity
     */
    public function getCostForQuantity(int $quantity, bool $useCoins = true): float|int
    {
        if ($useCoins && $this->canBePurchasedWithCoins()) {
            return $this->coin_cost * $quantity;
        }
        
        if ($this->canBePurchasedWithCash()) {
            return $this->price * $quantity;
        }
        
        return 0;
    }
}


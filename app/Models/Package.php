<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;

class Package extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'price',
        'type',
        'coins_amount',
        'status',
        'description',
        'image',
        'meta'
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'price' => 'decimal:2',
        'coins_amount' => 'integer',
        'meta' => 'array'
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    // Type constants
    const TYPE_STARTER = 'starter';
    const TYPE_PREMIUM = 'premium';
    const TYPE_BULK = 'bulk';
    const TYPE_SPECIAL = 'special';

    /**
     * Get all orders for this package
     */
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'target');
    }

    /**
     * Scope to get only active packages
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to get packages by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if package is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'SAR ' . number_format($this->price, 2);
    }

    /**
     * Get coins per SAR ratio
     */
    public function getCoinsPerSarAttribute(): float
    {
        return $this->price > 0 ? $this->coins_amount / $this->price : 0;
    }
}

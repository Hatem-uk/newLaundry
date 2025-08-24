<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Package;
use App\Models\Service;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_id',
        'provider_id',
        'target_id',
        'target_type',
        'coins',
        'price',
        'status',
        'meta'
    ];

    protected $casts = [
        'coins' => 'integer',
        'price' => 'decimal:2',
        'status' => 'string',
        'meta' => 'array'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROCESS = 'in_process';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';

    // Target type constants
    const TARGET_TYPE_PACKAGE = 'package';
    const TARGET_TYPE_SERVICE = 'service';

    /**
     * Get the user who initiated and pays for the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recipient of the value (defaults to user if not specified)
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the provider (seller: admin, laundry, or agent)
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Get the target package
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'target_id');
    }

    /**
     * Get the target service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'target_id');
    }

    /**
     * Get the target polymorphic relationship
     */
    public function target()
    {
        return $this->morphTo('target', 'target_type', 'target_id')
            ->morphWith([
                'package' => Package::class,
                'Package' => Package::class,
                'service' => Service::class,
                'Service' => Service::class,
            ]);
    }

    /**
     * Get the invoice for this order
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Scope to get orders by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get orders by target type
     */
    public function scopeByTargetType($query, $targetType)
    {
        return $query->where('target_type', $targetType);
    }

    /**
     * Scope to get orders by provider
     */
    public function scopeByProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    /**
     * Scope to get orders by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get orders by recipient
     */
    public function scopeByRecipient($query, $recipientId)
    {
        return $query->where('recipient_id', $recipientId);
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if order is in process
     */
    public function isInProcess(): bool
    {
        return $this->status === self::STATUS_IN_PROCESS;
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if order is canceled
     */
    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    /**
     * Check if order is for a package
     */
    public function isPackage(): bool
    {
        return $this->target_type === self::TARGET_TYPE_PACKAGE;
    }

    /**
     * Check if order is for a service
     */
    public function isService(): bool
    {
        return $this->target_type === self::TARGET_TYPE_SERVICE;
    }

    /**
     * Check if order involves coins
     */
    public function involvesCoins(): bool
    {
        return $this->coins !== 0;
    }

    /**
     * Check if order involves cash
     */
    public function involvesCash(): bool
    {
        return $this->price > 0;
    }

    /**
     * Get total value of the order
     */
    public function getTotalValueAttribute(): float
    {
        $total = 0;
        
        if ($this->involvesCash()) {
            $total += $this->price;
        }
        
        // Convert coins to approximate cash value (you might want to make this configurable)
        if ($this->involvesCoins()) {
            $coinValue = abs($this->coins) * 0.01; // Assuming 1 coin = 0.01 SAR
            $total += $coinValue;
        }
        
        return $total;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROCESS => 'In Process',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELED => 'Canceled',
            default => 'Unknown'
        };
    }

    /**
     * Mark order as in process
     */
    public function markInProcess(): void
    {
        $this->update(['status' => self::STATUS_IN_PROCESS]);
    }

    /**
     * Mark order as completed
     */
    public function markCompleted(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /**
     * Cancel order
     */
    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELED]);
    }
}

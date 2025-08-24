<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'payer_id',
        'provider_id',
        'amount',
        'payment_method',
        'status',
        'meta'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'meta' => 'array'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_REFUNDED = 'refunded';

    // Payment method constants
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_ONLINE = 'online';
    const PAYMENT_METHOD_COINS = 'coins';

    /**
     * Get the order this invoice belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user (recipient) this invoice is for
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who paid for this invoice
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    /**
     * Get the provider (seller) this invoice is from
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Scope to get invoices by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get invoices by payment method
     */
    public function scopeByPaymentMethod($query, $paymentMethod)
    {
        return $query->where('payment_method', $paymentMethod);
    }

    /**
     * Scope to get invoices by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get invoices by payer
     */
    public function scopeByPayer($query, $payerId)
    {
        return $query->where('payer_id', $payerId);
    }

    /**
     * Scope to get invoices by provider
     */
    public function scopeByProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    /**
     * Check if invoice is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if invoice is refunded
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Check if payment method is cash
     */
    public function isCashPayment(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_CASH;
    }

    /**
     * Check if payment method is online
     */
    public function isOnlinePayment(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_ONLINE;
    }

    /**
     * Check if payment method is coins
     */
    public function isCoinPayment(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_COINS;
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(): void
    {
        $this->update(['status' => self::STATUS_PAID]);
    }

    /**
     * Mark invoice as refunded
     */
    public function markAsRefunded(): void
    {
        $this->update(['status' => self::STATUS_REFUNDED]);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'SAR ' . number_format($this->amount, 2);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_REFUNDED => 'Refunded',
            default => 'Unknown'
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            self::PAYMENT_METHOD_CASH => 'Cash',
            self::PAYMENT_METHOD_ONLINE => 'Online',
            self::PAYMENT_METHOD_COINS => 'Coins',
            default => 'Unknown'
        };
    }
}

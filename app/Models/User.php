<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'fcm_tocken',
        'password',
        'role',
        'status',
        'email_verified_at'
    ];

    public function admin() {return $this->hasOne(Admin::class);  }
    public function agent() {return $this->hasOne(Agent::class);  }
    public function laundry() {return $this->hasOne(Laundry::class);  }
    public function worker() {return $this->hasOne(Worker::class);  }
    public function customer() {return $this->hasOne(Customer::class);  }

    // New relationships for the coin economy
    public function orders() { return $this->hasMany(Order::class); }
    public function receivedOrders() { return $this->hasMany(Order::class, 'recipient_id'); }
    public function providedOrders() { return $this->hasMany(Order::class, 'provider_id'); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function paidInvoices() { return $this->hasMany(Invoice::class, 'payer_id'); }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Add coins to customer's balance (only for customer role)
     */
    public function addCoins(int $amount): void
    {
        if ($this->role === 'customer' && $this->customer) {
            $this->customer->increment('coins', $amount);
        }
    }

    /**
     * Deduct coins from customer's balance (only for customer role)
     */
    public function deductCoins(int $amount): bool
    {
        if ($this->role === 'customer' && $this->customer && $this->customer->coins >= $amount) {
            $this->customer->decrement('coins', $amount);
            return true;
        }
        return false;
    }

    /**
     * Get user's coin balance (from customer relationship if customer role)
     */
    public function getCoinsAttribute()
    {
        if ($this->role === 'customer' && $this->customer) {
            return $this->customer->coins;
        }
        return 0;
    }

    /**
     * Check if user has enough coins
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'coins' => 'integer'
        ];
    }
}

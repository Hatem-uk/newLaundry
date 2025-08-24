<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'laundry_id',
        'rating',
        'comment',
        'service_type',
        'order_id'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * العلاقة مع العميل
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * العلاقة مع المغسلة
     */
    public function laundry(): BelongsTo
    {
        return $this->belongsTo(Laundry::class);
    }

    /**
     * العلاقة مع الطلب
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * نطاق التقييمات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('rating', '>', 0);
    }

    /**
     * نطاق التقييمات حسب النوع
     */
    public function scopeByServiceType($query, $type)
    {
        return $query->where('service_type', $type);
    }

    /**
     * نطاق التقييمات حسب المغسلة
     */
    public function scopeByLaundry($query, $laundryId)
    {
        return $query->where('laundry_id', $laundryId);
    }

    /**
     * نطاق التقييمات حسب العميل
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * التحقق من أن العميل لم يقيم هذه المغسلة من قبل لنفس الطلب
     */
    public static function hasCustomerRated($customerId, $laundryId, $orderId = null)
    {
        $query = static::where('customer_id', $customerId)
            ->where('laundry_id', $laundryId);

        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        return $query->exists();
    }

    /**
     * الحصول على متوسط التقييم لمغسلة
     */
    public static function getAverageRating($laundryId)
    {
        return static::where('laundry_id', $laundryId)
            ->where('rating', '>', 0)
            ->avg('rating');
    }

    /**
     * الحصول على عدد التقييمات لمغسلة
     */
    public static function getRatingCount($laundryId)
    {
        return static::where('laundry_id', $laundryId)
            ->where('rating', '>', 0)
            ->count();
    }

    /**
     * الحصول على توزيع التقييمات لمغسلة
     */
    public static function getRatingDistribution($laundryId)
    {
        return static::where('laundry_id', $laundryId)
            ->where('rating', '>', 0)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();
    }
}

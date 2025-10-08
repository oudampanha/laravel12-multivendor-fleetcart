<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_slug',
        'store_email',
        'store_phone',
        'store_address',
        'store_city',
        'store_state',
        'store_country',
        'store_zip',
        'commission_rate',
        'is_active',
        'is_verified',
        'verified_at',
        'balance',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_routing_number',
        'paypal_email',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'balance' => 'decimal:4',
        ];
    }

    /**
     * Get the user that owns the vendor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the vendor.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the coupons for the vendor.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Get the orders for the vendor.
     */
    public function vendorOrders(): HasMany
    {
        return $this->hasMany(VendorOrder::class);
    }

    /**
     * Get the order products for the vendor.
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the payouts for the vendor.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(VendorPayout::class);
    }

    /**
     * Get the withdrawals for the vendor.
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(VendorWithdrawal::class);
    }

    /**
     * Get the reviews for the vendor.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(VendorReview::class);
    }

    /**
     * Get the notifications for the vendor.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(VendorNotification::class);
    }

    /**
     * Get the shipping zones for the vendor.
     */
    public function shippingZones(): HasMany
    {
        return $this->hasMany(VendorShippingZone::class);
    }

    /**
     * Get the settings for the vendor.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(VendorSetting::class);
    }

    /**
     * Scope a query to only include active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include verified vendors.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Get a specific setting value.
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->settings()->where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a specific setting value.
     */
    public function setSetting(string $key, $value): void
    {
        $this->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Calculate vendor earnings from an order amount.
     */
    public function calculateEarnings(float $amount): float
    {
        return $amount * (1 - ($this->commission_rate / 100));
    }

    /**
     * Calculate commission from an order amount.
     */
    public function calculateCommission(float $amount): float
    {
        return $amount * ($this->commission_rate / 100);
    }
}

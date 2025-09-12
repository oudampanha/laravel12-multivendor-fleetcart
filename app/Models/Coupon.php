<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'code',
        'value',
        'is_percent',
        'free_shipping',
        'minimum_spend',
        'maximum_spend',
        'usage_limit_per_coupon',
        'usage_limit_per_customer',
        'used',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'is_percent' => 'boolean',
        'free_shipping' => 'boolean',
        'minimum_spend' => 'decimal:4',
        'maximum_spend' => 'decimal:4',
        'usage_limit_per_coupon' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'used' => 'integer',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_categories')
            ->withPivot('exclude')
            ->withTimestamps();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_products')
            ->withPivot('exclude')
            ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Coupon::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('vendor_id');
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public function scopeValid($query)
    {
        $now = now();
        
        return $query->where('is_active', true)
                    ->where(function ($query) use ($now) {
                        $query->whereNull('start_date')
                              ->orWhere('start_date', '<=', $now);
                    })
                    ->where(function ($query) use ($now) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', $now);
                    });
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isExpired(): bool
    {
        if (!$this->end_date) {
            return false;
        }

        return now()->gt($this->end_date);
    }

    public function isNotStarted(): bool
    {
        if (!$this->start_date) {
            return false;
        }

        return now()->lt($this->start_date);
    }

    public function isValid(): bool
    {
        return $this->isActive() && !$this->isExpired() && !$this->isNotStarted();
    }

    public function isGlobal(): bool
    {
        return is_null($this->vendor_id);
    }

    public function hasUsageLimit(): bool
    {
        return !is_null($this->usage_limit_per_coupon);
    }

    public function hasCustomerUsageLimit(): bool
    {
        return !is_null($this->usage_limit_per_customer);
    }

    public function isUsageLimitReached(): bool
    {
        if (!$this->hasUsageLimit()) {
            return false;
        }

        return $this->used >= $this->usage_limit_per_coupon;
    }

    public function canBeUsed(): bool
    {
        return $this->isValid() && !$this->isUsageLimitReached();
    }

    public function isPercent(): bool
    {
        return $this->is_percent;
    }

    public function isFixed(): bool
    {
        return !$this->is_percent;
    }

    public function hasMinimumSpend(): bool
    {
        return !is_null($this->minimum_spend) && $this->minimum_spend > 0;
    }

    public function hasMaximumSpend(): bool
    {
        return !is_null($this->maximum_spend) && $this->maximum_spend > 0;
    }

    public function offersFreeShipping(): bool
    {
        return $this->free_shipping;
    }

    public function getDiscountAmount(float $subtotal): float
    {
        if (!$this->canBeUsed()) {
            return 0;
        }

        if ($this->hasMinimumSpend() && $subtotal < $this->minimum_spend) {
            return 0;
        }

        $discountableAmount = $subtotal;

        if ($this->hasMaximumSpend() && $subtotal > $this->maximum_spend) {
            $discountableAmount = $this->maximum_spend;
        }

        if ($this->isPercent()) {
            return $discountableAmount * ($this->value / 100);
        }

        return min($this->value, $discountableAmount);
    }

    public function incrementUsage(): void
    {
        $this->increment('used');
    }

    public function decrementUsage(): void
    {
        $this->decrement('used');
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
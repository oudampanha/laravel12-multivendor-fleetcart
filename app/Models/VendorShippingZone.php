<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'countries',
        'states',
        'zip_codes',
        'shipping_method',
        'rate',
        'minimum_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'countries' => 'array',
            'states' => 'array',
            'zip_codes' => 'array',
            'rate' => 'decimal:4',
            'minimum_order' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the vendor that owns the shipping zone.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Scope a query to only include active shipping zones.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if a location is covered by this shipping zone.
     */
    public function coversLocation(string $country, string $state = null, string $zipCode = null): bool
    {
        if (!in_array($country, $this->countries)) {
            return false;
        }

        if ($this->states && $state && !in_array($state, $this->states)) {
            return false;
        }

        if ($this->zip_codes && $zipCode && !in_array($zipCode, $this->zip_codes)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate shipping cost for an order amount.
     */
    public function calculateShippingCost(float $orderAmount): float
    {
        if ($this->shipping_method === 'free_shipping') {
            return 0;
        }

        if ($this->minimum_order && $orderAmount < $this->minimum_order) {
            return $this->rate ?? 0;
        }

        if ($this->shipping_method === 'flat_rate') {
            return $this->rate ?? 0;
        }

        return 0;
    }
}
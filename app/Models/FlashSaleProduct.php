<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FlashSaleProduct extends Model
{
    protected $fillable = [
        'flash_sale_id',
        'product_id',
        'end_date',
        'price',
        'qty',
        'position',
    ];

    protected $casts = [
        'end_date' => 'date',
        'price' => 'decimal:4',
        'qty' => 'integer',
        'position' => 'integer',
    ];

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'flash_sale_product_orders')
            ->withPivot('qty')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('end_date', '>=', now())
            ->where('qty', '>', 0);
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeSoldOut($query)
    {
        return $query->where('qty', '<=', 0);
    }

    public function scopeAvailable($query)
    {
        return $query->where('end_date', '>=', now())
            ->where('qty', '>', 0);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    public function scopeByFlashSale($query, int $flashSaleId)
    {
        return $query->where('flash_sale_id', $flashSaleId);
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function isActive(): bool
    {
        return ! $this->isExpired() && ! $this->isSoldOut();
    }

    public function isExpired(): bool
    {
        return Carbon::parse($this->end_date)->isPast();
    }

    public function isSoldOut(): bool
    {
        return $this->qty <= 0;
    }

    public function isAvailable(): bool
    {
        return $this->isActive();
    }

    public function getRemainingTime(): ?Carbon
    {
        if ($this->isExpired()) {
            return null;
        }

        return Carbon::parse($this->end_date);
    }

    public function getRemainingTimeInHours(): int
    {
        $remainingTime = $this->getRemainingTime();

        if (! $remainingTime) {
            return 0;
        }

        return now()->diffInHours($remainingTime);
    }

    public function getRemainingTimeInMinutes(): int
    {
        $remainingTime = $this->getRemainingTime();

        if (! $remainingTime) {
            return 0;
        }

        return now()->diffInMinutes($remainingTime);
    }

    public function getDiscountAmount(): float
    {
        if (! $this->product) {
            return 0;
        }

        return $this->product->price - $this->price;
    }

    public function getDiscountPercentage(): float
    {
        if (! $this->product || $this->product->price <= 0) {
            return 0;
        }

        $discount = $this->getDiscountAmount();

        return round(($discount / $this->product->price) * 100, 2);
    }

    public function getSoldQuantity(): int
    {
        return $this->orders()->sum('flash_sale_product_orders.qty');
    }

    public function getRemainingQuantity(): int
    {
        return max(0, $this->qty - $this->getSoldQuantity());
    }

    public function getSalesPercentage(): float
    {
        if ($this->qty <= 0) {
            return 0;
        }

        return round(($this->getSoldQuantity() / $this->qty) * 100, 2);
    }

    public function canPurchase(int $requestedQty = 1): bool
    {
        return $this->isAvailable() && $this->getRemainingQuantity() >= $requestedQty;
    }

    public function purchaseQuantity(int $qty, Order $order): bool
    {
        if (! $this->canPurchase($qty)) {
            return false;
        }

        // Add to pivot table
        $this->orders()->attach($order->id, ['qty' => $qty]);

        return true;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2);
    }

    public function getFormattedOriginalPrice(): string
    {
        return $this->product ? number_format($this->product->price, 2) : '0.00';
    }

    public function getFormattedDiscount(): string
    {
        return number_format($this->getDiscountAmount(), 2);
    }

    public function getStatus(): string
    {
        if ($this->isSoldOut()) {
            return 'sold_out';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        return 'active';
    }

    public function getStatusLabel(): string
    {
        switch ($this->getStatus()) {
            case 'sold_out':
                return 'Sold Out';
            case 'expired':
                return 'Expired';
            case 'active':
                return 'Active';
            default:
                return 'Unknown';
        }
    }
}

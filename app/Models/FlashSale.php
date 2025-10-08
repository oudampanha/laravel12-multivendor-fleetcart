<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    protected $fillable = [];

    public function products(): HasMany
    {
        return $this->hasMany(FlashSaleProduct::class);
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->active();
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', FlashSale::class);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('products', function ($query) {
            $query->active();
        });
    }

    public function hasActiveProducts(): bool
    {
        return $this->activeProducts()->exists();
    }

    public function getTotalProducts(): int
    {
        return $this->products()->count();
    }

    public function getActiveProductsCount(): int
    {
        return $this->activeProducts()->count();
    }

    public function getExpiredProductsCount(): int
    {
        return $this->products()->expired()->count();
    }

    public function getSoldOutProductsCount(): int
    {
        return $this->products()->soldOut()->count();
    }

    public function getAvailableProductsCount(): int
    {
        return $this->products()->available()->count();
    }

    public function isActive(): bool
    {
        return $this->hasActiveProducts();
    }

    public function getProductsByPosition()
    {
        return $this->products()->orderBy('position', 'asc')->get();
    }

    public function getTopSellingProducts(int $limit = 10)
    {
        return $this->products()
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTotalRevenue(): float
    {
        return $this->products()
            ->with('orders')
            ->get()
            ->sum(function ($product) {
                return $product->orders->sum('pivot.qty') * $product->price;
            });
    }

    public function getTotalSales(): int
    {
        return $this->products()
            ->with('orders')
            ->get()
            ->sum(function ($product) {
                return $product->orders->sum('pivot.qty');
            });
    }
}

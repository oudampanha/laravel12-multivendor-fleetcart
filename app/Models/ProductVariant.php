<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uid',
        'uids',
        'product_id',
        'name',
        'price',
        'special_price',
        'special_price_type',
        'special_price_start',
        'special_price_end',
        'selling_price',
        'sku',
        'manage_stock',
        'qty',
        'in_stock',
        'is_default',
        'is_active',
        'position',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'special_price' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'qty' => 'integer',
        'position' => 'integer',
        'special_price_start' => 'date',
        'special_price_end' => 'date',
        'uids' => 'json',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function media()
    {
        return $this->morphToMany(Media::class, 'entity', 'entity_media')
            ->withPivot('zone')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->media()->wherePivot('zone', 'images');
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', ProductVariant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    public function getSellingPriceAttribute()
    {
        if ($this->hasSpecialPrice()) {
            return $this->getSpecialPrice();
        }

        return $this->price ?? $this->product->price;
    }

    public function hasSpecialPrice(): bool
    {
        if (is_null($this->special_price)) {
            return false;
        }

        $now = now();
        
        if ($this->special_price_start && $this->special_price_start > $now) {
            return false;
        }

        if ($this->special_price_end && $this->special_price_end < $now) {
            return false;
        }

        return true;
    }

    public function getSpecialPrice()
    {
        if (!$this->hasSpecialPrice()) {
            return $this->price ?? $this->product->price;
        }

        $basePrice = $this->price ?? $this->product->price;

        if ($this->special_price_type === 'percent') {
            return $basePrice - ($basePrice * $this->special_price / 100);
        }

        return $this->special_price;
    }

    public function isInStock(): bool
    {
        if (!$this->manage_stock && !$this->product->manage_stock) {
            return $this->in_stock ?? $this->product->in_stock;
        }

        return ($this->qty ?? 0) > 0;
    }
}
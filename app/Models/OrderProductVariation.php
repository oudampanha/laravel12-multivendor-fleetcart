<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrderProductVariation extends Model
{
    protected $fillable = [
        'order_product_id',
        'variation_id',
        'type',
        'value',
    ];

    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class);
    }

    public function variationValues(): BelongsToMany
    {
        return $this->belongsToMany(VariationValue::class, 'order_product_variation_values');
    }

    public function scopeByOrderProduct($query, int $orderProductId)
    {
        return $query->where('order_product_id', $orderProductId);
    }

    public function scopeByVariation($query, int $variationId)
    {
        return $query->where('variation_id', $variationId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function hasValue(): bool
    {
        return !empty(trim($this->value));
    }

    public function hasVariationValues(): bool
    {
        return $this->variationValues()->exists();
    }

    public function getFormattedValue(): string
    {
        if ($this->hasVariationValues()) {
            return $this->variationValues->pluck('value')->join(', ');
        }

        return $this->value ?? '';
    }

    public function getDisplayValue(): string
    {
        return $this->type . ': ' . $this->getFormattedValue();
    }
}
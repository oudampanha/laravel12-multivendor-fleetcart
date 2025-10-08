<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrderProductOption extends Model
{
    protected $fillable = [
        'order_product_id',
        'option_id',
        'value',
    ];

    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(OptionValue::class, 'order_product_option_values')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function scopeByOrderProduct($query, int $orderProductId)
    {
        return $query->where('order_product_id', $orderProductId);
    }

    public function scopeByOption($query, int $optionId)
    {
        return $query->where('option_id', $optionId);
    }

    public function hasValue(): bool
    {
        return ! empty(trim($this->value));
    }

    public function hasOptionValues(): bool
    {
        return $this->optionValues()->exists();
    }

    public function getTotalPrice(): float
    {
        return $this->optionValues()->sum('order_product_option_values.price');
    }

    public function getFormattedValue(): string
    {
        if ($this->hasOptionValues()) {
            return $this->optionValues->pluck('name')->join(', ');
        }

        return $this->value ?? '';
    }
}

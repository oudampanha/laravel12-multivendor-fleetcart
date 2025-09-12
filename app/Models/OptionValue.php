<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasTranslations;

class OptionValue extends Model
{
    use HasTranslations;
    protected $fillable = [
        'option_id',
        'price',
        'price_type',
        'position',
    ];

    protected array $translatable = ['name'];

    protected $casts = [
        'price' => 'decimal:4',
        'position' => 'integer',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }

    public function orderProductOptionValues(): BelongsToMany
    {
        return $this->belongsToMany(OrderProductOption::class, 'order_product_option_values')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', OptionValue::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    public function scopeByOption($query, int $optionId)
    {
        return $query->where('option_id', $optionId);
    }

    public function scopeByPriceType($query, string $priceType)
    {
        return $query->where('price_type', $priceType);
    }

    public function hasPrice(): bool
    {
        return !is_null($this->price) && $this->price > 0;
    }

    public function isPercentage(): bool
    {
        return $this->price_type === 'percent';
    }

    public function isFixed(): bool
    {
        return $this->price_type === 'fixed';
    }

    public function getCalculatedPrice(float $basePrice = 0): float
    {
        if (!$this->hasPrice()) {
            return 0;
        }

        if ($this->isPercentage()) {
            return $basePrice * ($this->price / 100);
        }

        return $this->price;
    }
}
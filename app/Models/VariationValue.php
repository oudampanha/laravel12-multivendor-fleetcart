<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariationValue extends Model
{
    protected $fillable = [
        'uid',
        'variation_id',
        'value',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class);
    }

    public function orderProductVariations(): BelongsToMany
    {
        return $this->belongsToMany(OrderProductVariation::class, 'order_product_variation_values');
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', VariationValue::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }
}
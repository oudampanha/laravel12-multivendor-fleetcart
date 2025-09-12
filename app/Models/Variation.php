<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use SoftDeletes, HasTranslations;

    protected $fillable = [
        'uid',
        'type',
        'is_global',
        'position',
    ];

    protected array $translatable = ['name'];

    protected $casts = [
        'is_global' => 'boolean',
        'position' => 'integer',
    ];

    public function variationValues(): HasMany
    {
        return $this->hasMany(VariationValue::class);
    }

    /**
     * Alias for variationValues relationship for backward compatibility
     */
    public function values(): HasMany
    {
        return $this->variationValues();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_variations');
    }

    public function orderProductVariations(): HasMany
    {
        return $this->hasMany(OrderProductVariation::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Variation::class);
    }

    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }
}
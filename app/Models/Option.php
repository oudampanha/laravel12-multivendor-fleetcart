<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'type',
        'is_required',
        'is_global',
        'position',
        'price',
        'price_type',
    ];

    protected array $translatable = ['name'];

    protected $casts = [
        'is_required' => 'boolean',
        'is_global' => 'boolean',
        'position' => 'integer',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(OptionValue::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_options');
    }

    public function orderProductOptions(): HasMany
    {
        return $this->hasMany(OrderProductOption::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Option::class);
    }

    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    public function isRequired(): bool
    {
        return $this->is_required;
    }

    public function isGlobal(): bool
    {
        return $this->is_global;
    }

    public function hasValues(): bool
    {
        return $this->values()->exists();
    }

    public function getValuesByPosition()
    {
        return $this->values()->orderBy('position')->get();
    }
}

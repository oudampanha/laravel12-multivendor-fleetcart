<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Brand::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function media()
    {
        return $this->morphToMany(Media::class, 'entity', 'entity_media')
            ->withPivot('zone')
            ->withTimestamps();
    }

    public function logo()
    {
        return $this->media()->wherePivot('zone', 'logo');
    }

    public function getLogoAttribute()
    {
        return $this->logo()->first();
    }
}

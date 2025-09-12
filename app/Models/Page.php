<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'entity', 'entity_type', 'entity_id');
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Page::class);
    }

    public function metaData(): HasMany
    {
        return $this->hasMany(MetaData::class, 'entity_id')
            ->where('entity_type', Page::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getUrl(): string
    {
        return route('pages.show', $this->slug);
    }

    public function getTitle(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            Page::class,
            $this->id,
            $locale,
            'title'
        ) ?? '';
    }

    public function getBody(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            Page::class,
            $this->id,
            $locale,
            'body'
        ) ?? '';
    }

    public function getMetaTitle(?string $locale = null): ?string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            Page::class,
            $this->id,
            $locale,
            'meta_title'
        );
    }

    public function getMetaDescription(?string $locale = null): ?string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            Page::class,
            $this->id,
            $locale,
            'meta_description'
        );
    }
}
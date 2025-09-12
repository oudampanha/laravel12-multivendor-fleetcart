<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SliderSlide extends Model
{
    protected $fillable = [
        'slider_id',
        'options',
        'call_to_action_url',
        'open_in_new_window',
        'position',
    ];

    protected $casts = [
        'options' => 'array',
        'open_in_new_window' => 'boolean',
        'position' => 'integer',
    ];

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'entity', 'entity_type', 'entity_id');
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', SliderSlide::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    public function getTitle(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            SliderSlide::class,
            $this->id,
            $locale,
            'title'
        ) ?? '';
    }

    public function getSubtitle(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            SliderSlide::class,
            $this->id,
            $locale,
            'subtitle'
        ) ?? '';
    }

    public function getDescription(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            SliderSlide::class,
            $this->id,
            $locale,
            'description'
        ) ?? '';
    }

    public function getCallToActionText(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            SliderSlide::class,
            $this->id,
            $locale,
            'call_to_action_text'
        ) ?? '';
    }

    public function getCallToActionUrl(): ?string
    {
        return $this->call_to_action_url;
    }

    public function hasCallToAction(): bool
    {
        return !empty($this->call_to_action_url);
    }

    public function opensInNewWindow(): bool
    {
        return $this->open_in_new_window ?? false;
    }

    public function getTargetAttribute(): string
    {
        return $this->opens_in_new_window ? '_blank' : '_self';
    }

    public function getOptions(): array
    {
        return $this->options ?? [];
    }

    public function getOption(string $key, $default = null)
    {
        return data_get($this->options, $key, $default);
    }

    public function hasImage(): bool
    {
        return $this->media()->exists();
    }

    public function getImageUrl(): ?string
    {
        $media = $this->media()->first();
        
        return $media ? $media->file_url : null;
    }

    public function getImage(): ?Media
    {
        return $this->media()->first();
    }
}
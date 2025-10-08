<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slider extends Model
{
    protected $fillable = [
        'speed',
        'autoplay',
        'autoplay_speed',
        'fade',
        'dots',
        'arrows',
    ];

    protected $casts = [
        'speed' => 'integer',
        'autoplay' => 'boolean',
        'autoplay_speed' => 'integer',
        'fade' => 'boolean',
        'dots' => 'boolean',
        'arrows' => 'boolean',
    ];

    public function slides(): HasMany
    {
        return $this->hasMany(SliderSlide::class);
    }

    public function activeSlides(): HasMany
    {
        return $this->hasMany(SliderSlide::class)
            ->orderBy('position', 'asc');
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Slider::class);
    }

    public function getName(?string $locale = null): string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            Slider::class,
            $this->id,
            $locale,
            'name'
        ) ?? "Slider {$this->id}";
    }

    public function hasSlides(): bool
    {
        return $this->slides()->exists();
    }

    public function getSlidesCount(): int
    {
        return $this->slides()->count();
    }

    public function getSliderConfig(): array
    {
        return [
            'speed' => $this->speed ?? 500,
            'autoplay' => $this->autoplay ?? false,
            'autoplaySpeed' => $this->autoplay_speed ?? 3000,
            'fade' => $this->fade ?? false,
            'dots' => $this->dots ?? true,
            'arrows' => $this->arrows ?? true,
            'infinite' => true,
            'slidesToShow' => 1,
            'slidesToScroll' => 1,
        ];
    }

    public function getSliderConfigJson(): string
    {
        return json_encode($this->getSliderConfig());
    }

    public function isAutoplay(): bool
    {
        return $this->autoplay ?? false;
    }

    public function isFade(): bool
    {
        return $this->fade ?? false;
    }

    public function hasDots(): bool
    {
        return $this->dots ?? true;
    }

    public function hasArrows(): bool
    {
        return $this->arrows ?? true;
    }
}

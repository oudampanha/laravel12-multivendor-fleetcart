<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MetaData extends Model
{
    protected $table = 'meta_data';

    protected $fillable = [
        'entity_type',
        'entity_id',
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', MetaData::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->translations();
    }

    public function scopeForEntity($query, string $entityType, int $entityId)
    {
        return $query->where('entity_type', $entityType)
            ->where('entity_id', $entityId);
    }

    public function getTitle(?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'title'
        );
    }

    public function getDescription(?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'description'
        );
    }

    public function getKeywords(?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'keywords'
        );
    }

    public function getOgTitle(?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'og_title'
        );
    }

    public function getOgDescription(?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'og_description'
        );
    }

    public function getOgImage(?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'og_image'
        );
    }

    public function getTwitterTitle(?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'twitter_title'
        );
    }

    public function getTwitterDescription(?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'twitter_description'
        );
    }

    public function getTwitterImage(?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'twitter_image'
        );
    }

    public function setTitle(string $value, ?string $locale = null): void
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        Translation::setTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'title',
            $value
        );
    }

    public function setDescription(string $value, ?string $locale = null): void
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        Translation::setTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'description',
            $value
        );
    }

    public function setKeywords(string $value, ?string $locale = null): void
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        Translation::setTranslation(
            MetaData::class,
            $this->id,
            $locale,
            'keywords',
            $value
        );
    }

    public function getAllMetaData(?string $locale = null): array
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        return [
            'title' => $this->getTitle($locale),
            'description' => $this->getDescription($locale),
            'keywords' => $this->getKeywords($locale),
            'og_title' => $this->getOgTitle($locale),
            'og_description' => $this->getOgDescription($locale),
            'og_image' => $this->getOgImage($locale),
            'twitter_title' => $this->getTwitterTitle($locale),
            'twitter_description' => $this->getTwitterDescription($locale),
            'twitter_image' => $this->getTwitterImage($locale),
        ];
    }

    public function setAllMetaData(array $data, ?string $locale = null): void
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $fields = [
            'title',
            'description',
            'keywords',
            'og_title',
            'og_description',
            'og_image',
            'twitter_title',
            'twitter_description',
            'twitter_image',
        ];

        foreach ($fields as $field) {
            if (isset($data[$field]) && ! empty($data[$field])) {
                Translation::setTranslation(
                    MetaData::class,
                    $this->id,
                    $locale,
                    $field,
                    $data[$field]
                );
            }
        }
    }

    public static function getForEntity(string $entityType, int $entityId): ?self
    {
        return static::forEntity($entityType, $entityId)->first();
    }

    public static function createForEntity(string $entityType, int $entityId): self
    {
        return static::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
        ]);
    }

    public static function getOrCreateForEntity(string $entityType, int $entityId): self
    {
        return static::firstOrCreate([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
        ]);
    }

    public function hasTitle(?string $locale = null): bool
    {
        return ! empty($this->getTitle($locale));
    }

    public function hasDescription(?string $locale = null): bool
    {
        return ! empty($this->getDescription($locale));
    }

    public function hasKeywords(?string $locale = null): bool
    {
        return ! empty($this->getKeywords($locale));
    }

    public function hasOpenGraphData(?string $locale = null): bool
    {
        return ! empty($this->getOgTitle($locale)) ||
            ! empty($this->getOgDescription($locale)) ||
            ! empty($this->getOgImage($locale));
    }

    public function hasTwitterData(?string $locale = null): bool
    {
        return ! empty($this->getTwitterTitle($locale)) ||
            ! empty($this->getTwitterDescription($locale)) ||
            ! empty($this->getTwitterImage($locale));
    }
}

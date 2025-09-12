<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'locale',
        'field',
        'value',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForModel($query, string $model, int $id)
    {
        return $query->where('translatable_type', $model)
                    ->where('translatable_id', $id);
    }

    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    public function scopeForField($query, string $field)
    {
        return $query->where('field', $field);
    }

    public function scopeByKey($query, string $model, int $id, string $locale, string $field)
    {
        return $query->where('translatable_type', $model)
                    ->where('translatable_id', $id)
                    ->where('locale', $locale)
                    ->where('field', $field);
    }

    public static function getTranslation(string $model, int $id, string $locale, string $field): ?string
    {
        $translation = static::byKey($model, $id, $locale, $field)->first();
        
        return $translation ? $translation->value : null;
    }

    public static function setTranslation(string $model, int $id, string $locale, string $field, string $value): self
    {
        return static::updateOrCreate(
            [
                'translatable_type' => $model,
                'translatable_id' => $id,
                'locale' => $locale,
                'field' => $field,
            ],
            ['value' => $value]
        );
    }

    public static function getTranslations(string $model, int $id, string $locale): array
    {
        return static::forModel($model, $id)
                    ->forLocale($locale)
                    ->pluck('value', 'field')
                    ->toArray();
    }

    public static function setTranslations(string $model, int $id, string $locale, array $translations): void
    {
        foreach ($translations as $field => $value) {
            if (!empty($value)) {
                static::setTranslation($model, $id, $locale, $field, $value);
            }
        }
    }

    public static function deleteTranslations(string $model, int $id): bool
    {
        return static::forModel($model, $id)->delete();
    }

    public static function getAvailableLocales(string $model, int $id): array
    {
        return static::forModel($model, $id)
                    ->distinct('locale')
                    ->pluck('locale')
                    ->toArray();
    }

    public static function hasTranslation(string $model, int $id, string $locale, string $field): bool
    {
        return static::byKey($model, $id, $locale, $field)->exists();
    }

    public function hasValue(): bool
    {
        return !empty(trim($this->value));
    }
}
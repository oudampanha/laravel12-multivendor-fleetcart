<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LanguageLine extends Model
{
    protected $fillable = [
        'group',
        'key',
        'text',
    ];

    protected $casts = [
        'text' => 'array',
    ];

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    public function scopeByGroupAndKey($query, string $group, string $key)
    {
        return $query->where('group', $group)->where('key', $key);
    }

    public function scopeHasTranslationFor($query, string $locale)
    {
        return $query->whereJsonContains('text->'.$locale, '!=', null);
    }

    public function scopeMissingTranslationFor($query, string $locale)
    {
        return $query->where(function ($q) use ($locale) {
            $q->whereJsonMissing('text->'.$locale)
                ->orWhereJsonContains('text->'.$locale, null)
                ->orWhereJsonContains('text->'.$locale, '');
        });
    }

    public static function getTranslation(string $group, string $key, ?string $locale = null, $default = null)
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $cacheKey = "language_line.{$group}.{$key}.{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($group, $key, $locale, $default) {
            $languageLine = static::byGroupAndKey($group, $key)->first();

            if (! $languageLine) {
                return $default;
            }

            return data_get($languageLine->text, $locale, $default);
        });
    }

    public static function setTranslation(string $group, string $key, string $locale, string $value): self
    {
        $languageLine = static::firstOrCreate(
            ['group' => $group, 'key' => $key],
            ['text' => []]
        );

        $text = $languageLine->text;
        $text[$locale] = $value;
        $languageLine->text = $text;
        $languageLine->save();

        // Clear cache
        Cache::forget("language_line.{$group}.{$key}.{$locale}");
        Cache::forget("language_lines.{$group}.{$locale}");
        Cache::forget("all_language_lines.{$locale}");

        return $languageLine;
    }

    public static function getGroupTranslations(string $group, ?string $locale = null): array
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $cacheKey = "language_lines.{$group}.{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($group, $locale) {
            return static::byGroup($group)
                ->get()
                ->mapWithKeys(function ($languageLine) use ($locale) {
                    return [
                        $languageLine->key => data_get($languageLine->text, $locale, ''),
                    ];
                })
                ->toArray();
        });
    }

    public static function getAllTranslations(?string $locale = null): array
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $cacheKey = "all_language_lines.{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($locale) {
            return static::all()
                ->groupBy('group')
                ->mapWithKeys(function ($groupLines, $group) use ($locale) {
                    return [
                        $group => $groupLines->mapWithKeys(function ($languageLine) use ($locale) {
                            return [
                                $languageLine->key => data_get($languageLine->text, $locale, ''),
                            ];
                        })->toArray(),
                    ];
                })
                ->toArray();
        });
    }

    public static function hasTranslation(string $group, string $key, string $locale): bool
    {
        $languageLine = static::byGroupAndKey($group, $key)->first();

        if (! $languageLine) {
            return false;
        }

        $translation = data_get($languageLine->text, $locale);

        return ! empty($translation);
    }

    public static function removeTranslation(string $group, string $key, ?string $locale = null): bool
    {
        $languageLine = static::byGroupAndKey($group, $key)->first();

        if (! $languageLine) {
            return false;
        }

        if ($locale) {
            // Remove specific locale
            $text = $languageLine->text;
            unset($text[$locale]);

            if (empty($text)) {
                // If no translations left, delete the language line
                $languageLine->delete();
            } else {
                $languageLine->text = $text;
                $languageLine->save();
            }

            // Clear cache
            Cache::forget("language_line.{$group}.{$key}.{$locale}");
        } else {
            // Remove entire language line
            $languageLine->delete();
        }

        // Clear group and global caches
        static::clearCacheForGroup($group);

        return true;
    }

    public static function getAvailableGroups(): array
    {
        return Cache::remember('language_line_groups', 3600, function () {
            return static::distinct('group')->pluck('group')->toArray();
        });
    }

    public static function getAvailableLocales(): array
    {
        return Cache::remember('language_line_locales', 3600, function () {
            $locales = [];

            static::all()->each(function ($languageLine) use (&$locales) {
                $locales = array_merge($locales, array_keys($languageLine->text));
            });

            return array_unique($locales);
        });
    }

    public static function getGroupStats(string $group): array
    {
        $lines = static::byGroup($group)->get();
        $locales = static::getAvailableLocales();

        $stats = [
            'total_keys' => $lines->count(),
            'locales' => [],
            'completion' => [],
        ];

        foreach ($locales as $locale) {
            $translatedCount = $lines->filter(function ($line) use ($locale) {
                return ! empty(data_get($line->text, $locale));
            })->count();

            $stats['locales'][$locale] = [
                'translated' => $translatedCount,
                'missing' => $stats['total_keys'] - $translatedCount,
                'percentage' => $stats['total_keys'] > 0
                    ? round(($translatedCount / $stats['total_keys']) * 100, 2)
                    : 0,
            ];
        }

        return $stats;
    }

    public static function getMissingTranslations(string $locale): array
    {
        return static::missingTranslationFor($locale)
            ->get()
            ->groupBy('group')
            ->mapWithKeys(function ($groupLines, $group) {
                return [
                    $group => $groupLines->pluck('key')->toArray(),
                ];
            })
            ->toArray();
    }

    public static function clearCache(): void
    {
        $groups = static::getAvailableGroups();
        $locales = static::getAvailableLocales();

        // Clear specific caches
        foreach ($groups as $group) {
            foreach ($locales as $locale) {
                Cache::forget("language_lines.{$group}.{$locale}");

                // Clear individual key caches
                $keys = static::byGroup($group)->pluck('key');
                foreach ($keys as $key) {
                    Cache::forget("language_line.{$group}.{$key}.{$locale}");
                }
            }
        }

        // Clear global caches
        foreach ($locales as $locale) {
            Cache::forget("all_language_lines.{$locale}");
        }

        Cache::forget('language_line_groups');
        Cache::forget('language_line_locales');
    }

    public static function clearCacheForGroup(string $group): void
    {
        $locales = static::getAvailableLocales();

        foreach ($locales as $locale) {
            Cache::forget("language_lines.{$group}.{$locale}");
            Cache::forget("all_language_lines.{$locale}");

            // Clear individual key caches
            $keys = static::byGroup($group)->pluck('key');
            foreach ($keys as $key) {
                Cache::forget("language_line.{$group}.{$key}.{$locale}");
            }
        }
    }

    public function getTranslationForLocale(string $locale): ?string
    {
        return data_get($this->text, $locale);
    }

    public function setTranslationForLocale(string $locale, string $value): void
    {
        $text = $this->text;
        $text[$locale] = $value;
        $this->text = $text;
        $this->save();

        // Clear relevant caches
        Cache::forget("language_line.{$this->group}.{$this->key}.{$locale}");
        Cache::forget("language_lines.{$this->group}.{$locale}");
        Cache::forget("all_language_lines.{$locale}");
    }

    public function hasTranslationForLocale(string $locale): bool
    {
        $translation = data_get($this->text, $locale);

        return ! empty($translation);
    }

    public function getAvailableLocalesForLine(): array
    {
        return array_keys(array_filter($this->text, function ($value) {
            return ! empty($value);
        }));
    }
}

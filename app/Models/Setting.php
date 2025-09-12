<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'is_translatable',
        'plain_value',
    ];

    protected $casts = [
        'is_translatable' => 'boolean',
    ];

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Setting::class);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    public function scopeTranslatable($query)
    {
        return $query->where('is_translatable', true);
    }

    public function scopeNotTranslatable($query)
    {
        return $query->where('is_translatable', false);
    }

    public function isTranslatable(): bool
    {
        return $this->is_translatable;
    }

    public function getValue(?string $locale = null)
    {
        if (!$this->is_translatable) {
            return $this->plain_value;
        }

        if (!$locale) {
            $locale = app()->getLocale();
        }

        $translation = Translation::getTranslation(
            Setting::class,
            $this->id,
            $locale,
            'value'
        );

        return $translation ?? $this->plain_value;
    }

    public function setValue($value, ?string $locale = null): void
    {
        if (!$this->is_translatable) {
            $this->update(['plain_value' => $value]);
        } else {
            if (!$locale) {
                $locale = app()->getLocale();
            }

            Translation::setTranslation(
                Setting::class,
                $this->id,
                $locale,
                'value',
                $value
            );
        }

        // Clear cache for this setting
        Cache::forget("setting.{$this->key}");
        Cache::forget("setting.{$this->key}.{$locale}");
    }

    public static function get(string $key, $default = null, ?string $locale = null)
    {
        $cacheKey = $locale ? "setting.{$key}.{$locale}" : "setting.{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default, $locale) {
            $setting = static::byKey($key)->first();

            if (!$setting) {
                return $default;
            }

            return $setting->getValue($locale);
        });
    }

    public static function set(string $key, $value, ?string $locale = null, bool $isTranslatable = false): void
    {
        $setting = static::firstOrCreate(
            ['key' => $key],
            ['is_translatable' => $isTranslatable]
        );

        $setting->setValue($value, $locale);
    }

    public static function has(string $key): bool
    {
        return static::byKey($key)->exists();
    }

    public static function forget(string $key): bool
    {
        $setting = static::byKey($key)->first();

        if ($setting) {
            // Clear all related caches
            Cache::forget("setting.{$key}");
            
            // Clear translated versions if applicable
            if ($setting->is_translatable) {
                $locales = Translation::forModel(Setting::class, $setting->id)
                    ->distinct('locale')
                    ->pluck('locale');
                
                foreach ($locales as $locale) {
                    Cache::forget("setting.{$key}.{$locale}");
                }
            }

            return $setting->delete();
        }

        return false;
    }

    public static function all(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            $settings = static::with('getTranslations')->get();
            $result = [];

            foreach ($settings as $setting) {
                if ($setting->is_translatable) {
                    // Get all translations for this setting
                    $translations = $setting->getTranslations->groupBy('locale');
                    foreach ($translations as $locale => $localeTranslations) {
                        $result[$locale][$setting->key] = $localeTranslations->first()->value ?? $setting->plain_value;
                    }
                    // Also add the plain value as fallback
                    $result['default'][$setting->key] = $setting->plain_value;
                } else {
                    $result['default'][$setting->key] = $setting->plain_value;
                }
            }

            return $result;
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('all_settings');
        
        // Clear individual setting caches - this is a simple approach
        // In production, you might want to use cache tags for more efficient clearing
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
            
            // If you need to clear all locale versions, you'd need to track them
            // This is a simplified version
        }
    }

    public function getAvailableLocales(): array
    {
        if (!$this->is_translatable) {
            return [];
        }

        return $this->getTranslations()
            ->distinct('locale')
            ->pluck('locale')
            ->toArray();
    }
}
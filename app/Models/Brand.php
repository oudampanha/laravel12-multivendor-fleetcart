<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasMetaData;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasMedia, HasMetaData, HasTranslations;

    /**
     * Media zones for this model
     */
    const MEDIA_ZONES = [
        'logo' => 'Brand Logo',
        'banner' => 'Brand Banner',
    ];

    protected $fillable = [
        'slug',
        'is_active',
    ];

    /**
     * Fields that can be translated
     */
    protected array $translatable = [
        'name',
        'description',
    ];

    /**
     * Meta fields that can be set
     */
    protected array $metaFields = [
        'meta_title',
        'meta_description',
        'meta_keywords',
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
        return $this->morphToMany(Media::class, 'entity', 'entity_media', 'entity_id', 'file_id')
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

    public function banner()
    {
        return $this->media()->wherePivot('zone', 'banner');
    }

    public function getBannerAttribute()
    {
        return $this->banner()->first();
    }

    // Generate unique slug
    public static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }
}

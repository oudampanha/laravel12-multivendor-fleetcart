<?php

namespace App\Traits;

use App\Models\MetaData;

trait HasMetaData
{
    public static function bootHasMetaData()
    {
        static::saved(function ($entity) {
            $entity->saveMetaData(request()->only(['meta_title', 'meta_description']));
        });

        // Auto-cleanup metadata and its translations when entity is deleted
        static::deleting(function ($entity) {
            if ($entity->meta && $entity->meta->exists) {
                // Delete all metadata translations first
                $entity->meta->translations()->delete();
                // Delete the metadata record
                $entity->meta->delete();
            }
        });
    }

    public function saveMetaData($data = [])
    {
        if (empty($data['meta_title']) && empty($data['meta_description'])) {
            return;
        }

        $meta = $this->resolveMetaData();

        $locale = app()->getLocale();

        // Save meta_title translation
        if (isset($data['meta_title'])) {
            \App\Models\Translation::setTranslation(
                \App\Models\MetaData::class,
                $meta->id,
                $locale,
                'title',
                $data['meta_title']
            );
        }

        // Save meta_description translation
        if (isset($data['meta_description'])) {
            \App\Models\Translation::setTranslation(
                \App\Models\MetaData::class,
                $meta->id,
                $locale,
                'description',
                $data['meta_description']
            );
        }
    }

    /**
     * Get the meta for the entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function meta()
    {
        return $this->morphOne(MetaData::class, 'entity')->withDefault();
    }

    /**
     * Get meta title for current locale
     */
    public function getMetaTitleAttribute()
    {
        $metaData = $this->meta;
        if (! $metaData || ! $metaData->exists) {
            return null;
        }

        return $metaData->getTitle(app()->getLocale());
    }

    /**
     * Get meta description for current locale
     */
    public function getMetaDescriptionAttribute()
    {
        $metaData = $this->meta;
        if (! $metaData || ! $metaData->exists) {
            return null;
        }

        return $metaData->getDescription(app()->getLocale());
    }

    /**
     * Get a specific meta field value
     */
    public function getMeta(string $field, ?string $locale = null): ?string
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $metaData = $this->meta;
        if (! $metaData || ! $metaData->exists) {
            return null;
        }

        // Map common field names to the method names
        $methodMap = [
            'meta_title' => 'getTitle',
            'meta_description' => 'getDescription',
            'meta_keywords' => 'getKeywords',
        ];

        if (isset($methodMap[$field]) && method_exists($metaData, $methodMap[$field])) {
            return $metaData->{$methodMap[$field]}($locale);
        }

        return null;
    }

    /**
     * Set a specific meta field value
     */
    public function setMeta(string $field, ?string $value, ?string $locale = null): void
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $metaData = $this->resolveMetaData();

        // Map common field names to the Translation field names
        $fieldMap = [
            'meta_title' => 'title',
            'meta_description' => 'description',
            'meta_keywords' => 'keywords',
        ];

        $translationField = $fieldMap[$field] ?? $field;

        if ($value) {
            \App\Models\Translation::setTranslation(
                \App\Models\MetaData::class,
                $metaData->id,
                $locale,
                $translationField,
                $value
            );
        }
    }

    protected function resolveMetaData(): MetaData
    {
        if ($this->relationLoaded('meta')) {
            $loadedMeta = $this->getRelation('meta');

            if ($loadedMeta instanceof MetaData && $loadedMeta->exists) {
                return $loadedMeta;
            }
        }

        $metaData = $this->meta()->first();

        if (! $metaData) {
            $metaData = $this->meta()->create([]);
        }

        $this->setRelation('meta', $metaData);

        return $metaData;
    }
}

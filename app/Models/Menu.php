<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function rootItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->orderBy('position', 'asc');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->where('is_active', true)
            ->orderBy('position', 'asc');
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Menu::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getName(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        return Translation::getTranslation(
            Menu::class,
            $this->id,
            $locale,
            'name'
        ) ?? 'Menu';
    }

    public function getMenuTree(?string $locale = null): array
    {
        $rootItems = $this->rootItems()
            ->where('is_active', true)
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('position', 'asc');
            }])
            ->get();

        return $this->buildMenuTree($rootItems, $locale);
    }

    protected function buildMenuTree($items, ?string $locale = null): array
    {
        $tree = [];

        foreach ($items as $item) {
            $itemData = [
                'id' => $item->id,
                'title' => $item->getTitle($locale),
                'url' => $item->getUrl(),
                'target' => $item->target,
                'icon' => $item->icon,
                'is_fluid' => $item->is_fluid,
                'children' => [],
            ];

            if ($item->children->isNotEmpty()) {
                $itemData['children'] = $this->buildMenuTree($item->children, $locale);
            }

            $tree[] = $itemData;
        }

        return $tree;
    }
}
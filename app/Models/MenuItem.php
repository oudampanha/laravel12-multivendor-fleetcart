<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id',
        'parent_id',
        'category_id',
        'page_id',
        'type',
        'url',
        'icon',
        'target',
        'position',
        'is_root',
        'is_fluid',
        'is_active',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_root' => 'boolean',
        'is_fluid' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', MenuItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isRoot(): bool
    {
        return $this->is_root || is_null($this->parent_id);
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function getTitle(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        $translation = Translation::getTranslation(
            MenuItem::class,
            $this->id,
            $locale,
            'title'
        );

        if ($translation) {
            return $translation;
        }

        // Fall back to related model titles
        switch ($this->type) {
            case 'category':
                if ($this->category) {
                    return Translation::getTranslation(
                        Category::class,
                        $this->category_id,
                        $locale,
                        'name'
                    ) ?? 'Category';
                }
                break;
            case 'page':
                if ($this->page) {
                    return $this->page->getTitle($locale);
                }
                break;
            case 'url':
                return $this->url ?? 'Link';
            default:
                return 'Menu Item';
        }

        return 'Menu Item';
    }

    public function getUrl(): string
    {
        switch ($this->type) {
            case 'category':
                if ($this->category) {
                    return route('categories.show', $this->category->slug);
                }
                break;
            case 'page':
                if ($this->page) {
                    return $this->page->getUrl();
                }
                break;
            case 'url':
                return $this->url ?? '#';
            default:
                return '#';
        }

        return '#';
    }

    public function getAncestors(): array
    {
        $ancestors = [];
        $menuItem = $this;

        while ($menuItem->parent) {
            $ancestors[] = $menuItem->parent;
            $menuItem = $menuItem->parent;
        }

        return array_reverse($ancestors);
    }

    public function getDescendants(): array
    {
        $descendants = [];

        foreach ($this->children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $child->getDescendants());
        }

        return $descendants;
    }

    public function getDepth(): int
    {
        return count($this->getAncestors());
    }
}
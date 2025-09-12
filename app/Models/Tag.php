<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use HasTranslations;

    protected $fillable = [
        'slug',
    ];

    protected array $translatable = ['name'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tags');
    }


    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function getProductsCount(): int
    {
        return $this->products()->count();
    }

    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    public static function createFromName(string $name): self
    {
        $slug = \Illuminate\Support\Str::slug($name);
        
        return static::create(['slug' => $slug]);
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
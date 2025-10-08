<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    protected $fillable = [
        'slug',
    ];

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', BlogCategory::class);
    }

    public function scopeWithPosts($query)
    {
        return $query->has('blogPosts');
    }
}

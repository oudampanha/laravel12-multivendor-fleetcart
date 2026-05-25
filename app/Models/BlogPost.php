<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    protected $fillable = [
        'user_id',
        'blog_category_id',
        'slug',
        'publish_status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function blogCategory(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function blogTags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_blog_tag');
    }

    public function media()
    {
        return $this->morphToMany(Media::class, 'entity', 'entity_media', 'entity_id', 'file_id')
            ->withPivot('zone')
            ->withTimestamps();
    }

    public function featuredImage()
    {
        return $this->media()->wherePivot('zone', 'featured_image');
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', BlogPost::class);
    }

    public function scopePublished($query)
    {
        return $query->where('publish_status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('publish_status', 'draft');
    }

    public function scopeByAuthor($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('blog_category_id', $categoryId);
    }

    public function scopeWithTags($query, array $tagIds)
    {
        return $query->whereHas('blogTags', function ($q) use ($tagIds) {
            $q->whereIn('blog_tags.id', $tagIds);
        });
    }

    public function isPublished(): bool
    {
        return $this->publish_status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->publish_status === 'draft';
    }

    public function getFeaturedImageAttribute()
    {
        return $this->featuredImage()->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = [
        'reviewer_id',
        'product_id',
        'rating',
        'reviewer_name',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Review::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeByReviewer($query, int $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeHighestRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    public function scopeLowestRated($query)
    {
        return $query->orderBy('rating', 'asc');
    }

    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    public function isPending(): bool
    {
        return !$this->is_approved;
    }

    public function approve(): bool
    {
        $this->is_approved = true;
        return $this->save();
    }

    public function disapprove(): bool
    {
        $this->is_approved = false;
        return $this->save();
    }

    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }

    public function isNeutral(): bool
    {
        return $this->rating === 3;
    }

    public function getStarRating(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function hasComment(): bool
    {
        return !empty(trim($this->comment));
    }

    public function getShortComment(int $length = 100): string
    {
        if (!$this->hasComment()) {
            return '';
        }

        return strlen($this->comment) > $length 
            ? substr($this->comment, 0, $length) . '...'
            : $this->comment;
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->reviewer_id === $user->id || $user->hasRole('admin');
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->reviewer_id === $user->id || $user->hasRole('admin');
    }

    public static function getAverageRating(int $productId): float
    {
        return static::where('product_id', $productId)
                    ->where('is_approved', true)
                    ->avg('rating') ?? 0;
    }

    public static function getTotalReviews(int $productId): int
    {
        return static::where('product_id', $productId)
                    ->where('is_approved', true)
                    ->count();
    }

    public static function getRatingDistribution(int $productId): array
    {
        $distribution = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = static::where('product_id', $productId)
                                     ->where('is_approved', true)
                                     ->where('rating', $i)
                                     ->count();
        }
        
        return $distribution;
    }

    public function updateRating(int $rating): bool
    {
        if ($rating < 1 || $rating > 5) {
            return false;
        }

        $this->rating = $rating;
        return $this->save();
    }
}
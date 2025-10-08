<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'customer_id',
        'order_id',
        'rating',
        'reviewer_name',
        'comment',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Get the vendor being reviewed.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the customer who wrote the review.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the order associated with the review.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Approve the review.
     */
    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    /**
     * Reject the review.
     */
    public function reject(): void
    {
        $this->update(['is_approved' => false]);
    }
}

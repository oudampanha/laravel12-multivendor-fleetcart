<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'amount',
        'status',
        'method',
        'reference_number',
        'note',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:4',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the vendor that owns the payout.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by method.
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', $method);
    }

    /**
     * Mark the payout as completed.
     */
    public function markAsCompleted(string $referenceNumber = null): void
    {
        $this->update([
            'status' => 'completed',
            'reference_number' => $referenceNumber,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark the payout as failed.
     */
    public function markAsFailed(string $note = null): void
    {
        $this->update([
            'status' => 'failed',
            'note' => $note,
        ]);
    }

    /**
     * Check if the payout is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the payout is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
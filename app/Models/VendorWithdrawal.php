<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'amount',
        'method',
        'status',
        'note',
        'admin_note',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:4',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Get the vendor that owns the withdrawal.
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
     * Mark the withdrawal as completed.
     */
    public function markAsCompleted(?string $adminNote = null): void
    {
        $this->update([
            'status' => 'completed',
            'admin_note' => $adminNote,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark the withdrawal as rejected.
     */
    public function markAsRejected(string $adminNote): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_note' => $adminNote,
            'processed_at' => now(),
        ]);
    }

    /**
     * Check if the withdrawal is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the withdrawal is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the withdrawal is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}

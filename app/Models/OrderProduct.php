<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderProduct extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'product_variant_id',
        'unit_price',
        'qty',
        'line_total',
        'vendor_commission',
        'vendor_status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:4',
        'qty' => 'integer',
        'line_total' => 'decimal:4',
        'vendor_commission' => 'decimal:4',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(OrderProductOption::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(OrderProductVariation::class);
    }

    public function scopeByOrder($query, int $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeByVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByVendorStatus($query, string $status)
    {
        return $query->where('vendor_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('vendor_status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('vendor_status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('vendor_status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('vendor_status', 'delivered');
    }

    public function scopeCanceled($query)
    {
        return $query->where('vendor_status', 'canceled');
    }

    public function scopeRefunded($query)
    {
        return $query->where('vendor_status', 'refunded');
    }

    public function getVendorEarnings(): float
    {
        return $this->line_total - $this->vendor_commission;
    }

    public function getTotalWithCommission(): float
    {
        return $this->line_total;
    }

    public function getCommissionPercentage(): float
    {
        if ($this->line_total <= 0) {
            return 0;
        }

        return ($this->vendor_commission / $this->line_total) * 100;
    }

    public function isPending(): bool
    {
        return $this->vendor_status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->vendor_status === 'processing';
    }

    public function isShipped(): bool
    {
        return $this->vendor_status === 'shipped';
    }

    public function isDelivered(): bool
    {
        return $this->vendor_status === 'delivered';
    }

    public function isCanceled(): bool
    {
        return $this->vendor_status === 'canceled';
    }

    public function isRefunded(): bool
    {
        return $this->vendor_status === 'refunded';
    }

    public function canBeCanceled(): bool
    {
        return in_array($this->vendor_status, ['pending', 'processing']);
    }

    public function canBeShipped(): bool
    {
        return $this->vendor_status === 'processing';
    }

    public function canBeDelivered(): bool
    {
        return $this->vendor_status === 'shipped';
    }

    public function canBeRefunded(): bool
    {
        return $this->vendor_status === 'delivered';
    }

    public function calculateCommission(): void
    {
        if (!$this->vendor) {
            $this->vendor_commission = 0;
            return;
        }

        $commissionRate = $this->vendor->commission_rate;
        $this->vendor_commission = $this->line_total * ($commissionRate / 100);
        $this->save();
    }

    public function hasOptions(): bool
    {
        return $this->options()->exists();
    }

    public function hasVariations(): bool
    {
        return $this->variations()->exists();
    }

    public function getOptionsTotal(): float
    {
        return $this->options()
            ->with('optionValues')
            ->get()
            ->sum(function ($option) {
                return $option->optionValues->sum('pivot.price');
            });
    }

    public function getFormattedUnitPrice(): string
    {
        return number_format($this->unit_price, 2);
    }

    public function getFormattedLineTotal(): string
    {
        return number_format($this->line_total, 2);
    }

    public function getFormattedCommission(): string
    {
        return number_format($this->vendor_commission, 2);
    }

    public function getFormattedVendorEarnings(): string
    {
        return number_format($this->getVendorEarnings(), 2);
    }

    public function updateStatus(string $status): bool
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'canceled', 'refunded'];

        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $this->vendor_status = $status;
        return $this->save();
    }
}
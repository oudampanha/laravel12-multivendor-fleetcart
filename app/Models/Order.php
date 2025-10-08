<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'customer_email',
        'customer_phone',
        'customer_first_name',
        'customer_last_name',
        'billing_first_name',
        'billing_last_name',
        'billing_address_1',
        'billing_address_2',
        'billing_city',
        'billing_state',
        'billing_zip',
        'billing_country',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'sub_total',
        'shipping_method',
        'shipping_cost',
        'coupon_id',
        'discount',
        'total',
        'payment_method',
        'currency',
        'currency_rate',
        'locale',
        'status',
        'note',
        'tracking_reference',
    ];

    protected $casts = [
        'sub_total' => 'decimal:4',
        'shipping_cost' => 'decimal:4',
        'discount' => 'decimal:4',
        'total' => 'decimal:4',
        'currency_rate' => 'decimal:4',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function vendorOrders(): HasMany
    {
        return $this->hasMany(VendorOrder::class);
    }

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(TaxRate::class, 'order_taxes')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(OrderDownload::class);
    }

    public function flashSaleProducts(): BelongsToMany
    {
        return $this->belongsToMany(FlashSaleProduct::class, 'flash_sale_product_orders')
            ->withPivot('qty');
    }

    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->customer_first_name.' '.$this->customer_last_name;
    }

    public function getBillingNameAttribute(): string
    {
        return $this->billing_first_name.' '.$this->billing_last_name;
    }

    public function getShippingNameAttribute(): string
    {
        return $this->shipping_first_name.' '.$this->shipping_last_name;
    }

    public function getBillingAddressAttribute(): string
    {
        $address = $this->billing_address_1;

        if ($this->billing_address_2) {
            $address .= ', '.$this->billing_address_2;
        }

        $address .= ', '.$this->billing_city;
        $address .= ', '.$this->billing_state;
        $address .= ' '.$this->billing_zip;
        $address .= ', '.$this->billing_country;

        return $address;
    }

    public function getShippingAddressAttribute(): string
    {
        $address = $this->shipping_address_1;

        if ($this->shipping_address_2) {
            $address .= ', '.$this->shipping_address_2;
        }

        $address .= ', '.$this->shipping_city;
        $address .= ', '.$this->shipping_state;
        $address .= ' '.$this->shipping_zip;
        $address .= ', '.$this->shipping_country;

        return $address;
    }

    public function getTotalTaxAmount(): float
    {
        return $this->taxes()->sum('amount');
    }

    public function getSubtotalWithoutTax(): float
    {
        return $this->sub_total - $this->getTotalTaxAmount();
    }

    public function hasDiscount(): bool
    {
        return $this->discount > 0;
    }

    public function hasCoupon(): bool
    {
        return ! is_null($this->coupon_id);
    }

    public function hasShipping(): bool
    {
        return $this->shipping_cost > 0;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function canBeCanceled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'completed';
    }

    public function getTotalItemsCount(): int
    {
        return $this->orderProducts()->sum('qty');
    }

    public function getUniqueVendorsCount(): int
    {
        return $this->orderProducts()->distinct('vendor_id')->count('vendor_id');
    }

    public function getVendorOrdersCount(): int
    {
        return $this->vendorOrders()->count();
    }

    public function hasMultipleVendors(): bool
    {
        return $this->getUniqueVendorsCount() > 1;
    }

    public function calculateCommission(): void
    {
        foreach ($this->orderProducts as $orderProduct) {
            $orderProduct->calculateCommission();
        }
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-'.strtoupper(uniqid());
    }

    public function getFormattedTotal(): string
    {
        return number_format($this->total, 2).' '.$this->currency;
    }

    public function getFormattedSubTotal(): string
    {
        return number_format($this->sub_total, 2).' '.$this->currency;
    }

    public function getFormattedDiscount(): string
    {
        return number_format($this->discount, 2).' '.$this->currency;
    }

    public function getFormattedShippingCost(): string
    {
        return number_format($this->shipping_cost, 2).' '.$this->currency;
    }
}

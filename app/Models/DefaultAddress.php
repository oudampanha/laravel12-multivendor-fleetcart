<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefaultAddress extends Model
{
    protected $fillable = [
        'customer_id',
        'address_id',
    ];

    public $timestamps = false;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public static function setForCustomer(int $customerId, int $addressId): void
    {
        // Verify the address belongs to the customer
        $address = Address::where('id', $addressId)
            ->where('customer_id', $customerId)
            ->first();

        if (! $address) {
            throw new \InvalidArgumentException('Address does not belong to the specified customer.');
        }

        // Remove existing default address
        static::where('customer_id', $customerId)->delete();

        // Set new default address
        static::create([
            'customer_id' => $customerId,
            'address_id' => $addressId,
        ]);
    }

    public static function getForCustomer(int $customerId): ?Address
    {
        $defaultAddress = static::forCustomer($customerId)
            ->with('address')
            ->first();

        return $defaultAddress ? $defaultAddress->address : null;
    }

    public static function hasDefaultForCustomer(int $customerId): bool
    {
        return static::forCustomer($customerId)->exists();
    }

    public static function removeForCustomer(int $customerId): bool
    {
        return static::where('customer_id', $customerId)->delete();
    }

    public static function getCustomersWithDefault(): \Illuminate\Database\Eloquent\Collection
    {
        return static::with(['customer', 'address'])->get();
    }

    public function isValidAddress(): bool
    {
        return $this->address &&
               $this->address->customer_id === $this->customer_id;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($defaultAddress) {
            // Ensure only one default address per customer
            static::where('customer_id', $defaultAddress->customer_id)->delete();
        });

        static::updating(function ($defaultAddress) {
            // Prevent updating if it would create duplicate defaults
            $existing = static::where('customer_id', $defaultAddress->customer_id)
                ->where('id', '!=', $defaultAddress->id)
                ->exists();

            if ($existing) {
                throw new \Exception('Customer already has a default address. Remove it first.');
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends Model
{
    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'address_1',
        'address_2',
        'city',
        'state',
        'zip',
        'country',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(DefaultAddress::class);
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByState($query, string $state)
    {
        return $query->where('state', $state);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_1,
            $this->address_2,
            $this->city,
            $this->state,
            $this->zip,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function getFormattedAddressAttribute(): array
    {
        return [
            'name' => $this->full_name,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
        ];
    }

    public function isDefault(): bool
    {
        return $this->defaultAddress()->exists();
    }

    public function makeDefault(): void
    {
        // Remove any existing default address for this customer
        DefaultAddress::where('customer_id', $this->customer_id)->delete();

        // Create new default address record
        DefaultAddress::create([
            'customer_id' => $this->customer_id,
            'address_id' => $this->id,
        ]);
    }

    public function removeAsDefault(): void
    {
        $this->defaultAddress()->delete();
    }

    public function isSameAs(self $address): bool
    {
        return $this->first_name === $address->first_name &&
               $this->last_name === $address->last_name &&
               $this->address_1 === $address->address_1 &&
               $this->address_2 === $address->address_2 &&
               $this->city === $address->city &&
               $this->state === $address->state &&
               $this->zip === $address->zip &&
               $this->country === $address->country;
    }

    public function getAddressLines(): array
    {
        $lines = [$this->address_1];
        
        if (!empty($this->address_2)) {
            $lines[] = $this->address_2;
        }

        return $lines;
    }

    public function getCityStateZip(): string
    {
        $parts = array_filter([$this->city, $this->state, $this->zip]);
        
        return implode(', ', $parts);
    }

    public function getCountryName(): string
    {
        // This could be expanded with a countries array or service
        // For now, returning the country code
        return $this->country;
    }

    public function isInCountry(string $country): bool
    {
        return strtoupper($this->country) === strtoupper($country);
    }

    public function isInState(string $state): bool
    {
        return strtoupper($this->state) === strtoupper($state);
    }

    public function isInCity(string $city): bool
    {
        return strtoupper($this->city) === strtoupper($city);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'full_name' => $this->full_name,
            'full_address' => $this->full_address,
            'formatted_address' => $this->formatted_address,
            'is_default' => $this->isDefault(),
        ]);
    }

    public static function createFromOrder(array $orderData, string $type = 'billing'): array
    {
        $prefix = $type === 'shipping' ? 'shipping_' : 'billing_';

        return [
            'first_name' => $orderData[$prefix . 'first_name'],
            'last_name' => $orderData[$prefix . 'last_name'],
            'address_1' => $orderData[$prefix . 'address_1'],
            'address_2' => $orderData[$prefix . 'address_2'] ?? null,
            'city' => $orderData[$prefix . 'city'],
            'state' => $orderData[$prefix . 'state'],
            'zip' => $orderData[$prefix . 'zip'],
            'country' => $orderData[$prefix . 'country'],
        ];
    }
}
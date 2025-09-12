<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Cart extends Model
{
    protected $fillable = [
        'id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function scopeBySessionId($query, string $sessionId)
    {
        return $query->where('id', $sessionId);
    }

    public function scopeExpired($query, int $hours = 24)
    {
        return $query->where('updated_at', '<', now()->subHours($hours));
    }

    public function getItems(): Collection
    {
        return collect($this->data['items'] ?? []);
    }

    public function getItem(string $rowId): ?array
    {
        return $this->getItems()->firstWhere('rowId', $rowId);
    }

    public function hasItem(string $rowId): bool
    {
        return $this->getItems()->contains('rowId', $rowId);
    }

    public function addItem(array $item): void
    {
        $items = $this->getItems();
        
        // Check if item with same product and options already exists
        $existingItem = $items->first(function ($cartItem) use ($item) {
            return $cartItem['product_id'] === $item['product_id'] &&
                   ($cartItem['product_variant_id'] ?? null) === ($item['product_variant_id'] ?? null) &&
                   json_encode($cartItem['options'] ?? []) === json_encode($item['options'] ?? []);
        });

        if ($existingItem) {
            // Update quantity of existing item
            $this->updateItemQuantity($existingItem['rowId'], $existingItem['qty'] + $item['qty']);
        } else {
            // Add new item
            $item['rowId'] = $this->generateRowId();
            $items->push($item);
            $this->updateData(['items' => $items->toArray()]);
        }
    }

    public function updateItem(string $rowId, array $data): bool
    {
        $items = $this->getItems();
        $itemIndex = $items->search(function ($item) use ($rowId) {
            return $item['rowId'] === $rowId;
        });

        if ($itemIndex === false) {
            return false;
        }

        $items[$itemIndex] = array_merge($items[$itemIndex], $data);
        $this->updateData(['items' => $items->toArray()]);

        return true;
    }

    public function updateItemQuantity(string $rowId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($rowId);
        }

        return $this->updateItem($rowId, ['qty' => $quantity]);
    }

    public function removeItem(string $rowId): bool
    {
        $items = $this->getItems();
        $filteredItems = $items->filter(function ($item) use ($rowId) {
            return $item['rowId'] !== $rowId;
        });

        if ($items->count() === $filteredItems->count()) {
            return false;
        }

        $this->updateData(['items' => $filteredItems->values()->toArray()]);

        return true;
    }

    public function clearItems(): void
    {
        $this->updateData(['items' => []]);
    }

    public function getItemsCount(): int
    {
        return $this->getItems()->sum('qty');
    }

    public function getTotalCount(): int
    {
        return $this->getItems()->count();
    }

    public function getSubTotal(): float
    {
        return $this->getItems()->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });
    }

    public function getTax(): float
    {
        return $this->data['tax'] ?? 0.0;
    }

    public function getShipping(): float
    {
        return $this->data['shipping'] ?? 0.0;
    }

    public function getDiscount(): float
    {
        return $this->data['discount'] ?? 0.0;
    }

    public function getTotal(): float
    {
        return $this->getSubTotal() + $this->getTax() + $this->getShipping() - $this->getDiscount();
    }

    public function isEmpty(): bool
    {
        return $this->getItems()->isEmpty();
    }

    public function hasShipping(): bool
    {
        return $this->getShipping() > 0;
    }

    public function hasDiscount(): bool
    {
        return $this->getDiscount() > 0;
    }

    public function setTax(float $tax): void
    {
        $data = $this->data;
        $data['tax'] = $tax;
        $this->updateData($data);
    }

    public function setShipping(float $shipping): void
    {
        $data = $this->data;
        $data['shipping'] = $shipping;
        $this->updateData($data);
    }

    public function setDiscount(float $discount): void
    {
        $data = $this->data;
        $data['discount'] = $discount;
        $this->updateData($data);
    }

    public function setCoupon(array $coupon): void
    {
        $data = $this->data;
        $data['coupon'] = $coupon;
        $this->updateData($data);
    }

    public function removeCoupon(): void
    {
        $data = $this->data;
        unset($data['coupon']);
        $data['discount'] = 0;
        $this->updateData($data);
    }

    public function getCoupon(): ?array
    {
        return $this->data['coupon'] ?? null;
    }

    public function hasCoupon(): bool
    {
        return !empty($this->data['coupon']);
    }

    public function getItemsByVendor(): Collection
    {
        return $this->getItems()->groupBy('vendor_id');
    }

    public function getVendorIds(): array
    {
        return $this->getItems()
            ->pluck('vendor_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    public static function createForSession(string $sessionId): self
    {
        return static::create([
            'id' => $sessionId,
            'data' => ['items' => []],
        ]);
    }

    public static function getForSession(string $sessionId): self
    {
        return static::firstOrCreate(
            ['id' => $sessionId],
            ['data' => ['items' => []]]
        );
    }

    public static function clearExpired(int $hours = 24): int
    {
        return static::expired($hours)->delete();
    }

    protected function updateData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
        $this->save();
    }

    protected function generateRowId(): string
    {
        return uniqid('cart_', true);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishList extends Model
{
    protected $table = 'wish_lists';

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeWithActiveProducts($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->where('is_active', true);
        });
    }

    public function scopeWithAvailableProducts($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->where('is_active', true)
                ->where('in_stock', true);
        });
    }

    public static function addToWishlist(int $userId, int $productId): bool
    {
        // Check if product exists and is active
        $product = Product::where('id', $productId)
            ->where('is_active', true)
            ->first();

        if (! $product) {
            return false;
        }

        // Check if already in wishlist
        if (static::isInWishlist($userId, $productId)) {
            return false;
        }

        static::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return true;
    }

    public static function removeFromWishlist(int $userId, int $productId): bool
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    public static function isInWishlist(int $userId, int $productId): bool
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    public static function toggleWishlist(int $userId, int $productId): bool
    {
        if (static::isInWishlist($userId, $productId)) {
            static::removeFromWishlist($userId, $productId);

            return false; // Removed from wishlist
        } else {
            return static::addToWishlist($userId, $productId); // Added to wishlist (true) or failed (false)
        }
    }

    public static function getWishlistForUser(int $userId)
    {
        return static::forUser($userId)
            ->with(['product' => function ($query) {
                $query->with(['media', 'brand', 'categories'])
                    ->where('is_active', true);
            }])
            ->latest()
            ->get();
    }

    public static function getWishlistCount(int $userId): int
    {
        return static::forUser($userId)
            ->withActiveProducts()
            ->count();
    }

    public static function clearWishlistForUser(int $userId): int
    {
        return static::where('user_id', $userId)->delete();
    }

    public static function getPopularProducts(int $limit = 10)
    {
        return static::select('product_id')
            ->selectRaw('COUNT(*) as wishlist_count')
            ->withActiveProducts()
            ->groupBy('product_id')
            ->orderByDesc('wishlist_count')
            ->limit($limit)
            ->with(['product' => function ($query) {
                $query->with(['media', 'brand', 'categories']);
            }])
            ->get()
            ->pluck('product');
    }

    public static function getUsersWhoWishlistedProduct(int $productId)
    {
        return static::forProduct($productId)
            ->with('user')
            ->get()
            ->pluck('user');
    }

    public static function moveToCart(int $userId, int $productId, string $sessionId): bool
    {
        if (! static::isInWishlist($userId, $productId)) {
            return false;
        }

        $product = Product::find($productId);
        if (! $product || ! $product->is_active || ! $product->in_stock) {
            return false;
        }

        $cart = Cart::getForSession($sessionId);

        // Add to cart
        $cart->addItem([
            'product_id' => $productId,
            'vendor_id' => $product->vendor_id,
            'name' => $product->name,
            'price' => $product->selling_price ?? $product->price,
            'qty' => 1,
            'options' => [],
        ]);

        // Remove from wishlist
        static::removeFromWishlist($userId, $productId);

        return true;
    }

    public static function getRecentlyAddedForUser(int $userId, int $limit = 5)
    {
        return static::forUser($userId)
            ->withActiveProducts()
            ->with(['product' => function ($query) {
                $query->with(['media', 'brand']);
            }])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function isProductAvailable(): bool
    {
        return $this->product &&
               $this->product->is_active &&
               $this->product->in_stock;
    }

    public function getProductPrice(): float
    {
        if (! $this->product) {
            return 0.0;
        }

        return $this->product->selling_price ?? $this->product->price ?? 0.0;
    }
}

<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'brand_id',
        'tax_class_id',
        'slug',
        'price',
        'special_price',
        'special_price_type',
        'special_price_start',
        'special_price_end',
        'selling_price',
        'sku',
        'manage_stock',
        'qty',
        'in_stock',
        'viewed',
        'is_active',
        'is_virtual',
        'new_from',
        'new_to',
        'vendor_status',
        'vendor_rejection_reason',
    ];

    /**
     * Fields that can be translated
     */
    protected array $translatable = [
        'name',
        'description',
        'short_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'special_price' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_virtual' => 'boolean',
        'viewed' => 'integer',
        'qty' => 'integer',
        'special_price_start' => 'date',
        'special_price_end' => 'date',
        'new_from' => 'datetime',
        'new_to' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function variations(): BelongsToMany
    {
        return $this->belongsToMany(Variation::class, 'product_variations');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'product_options');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id');
    }

    public function upSellProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'up_sell_products', 'product_id', 'up_sell_product_id');
    }

    public function crossSellProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'cross_sell_products', 'product_id', 'cross_sell_product_id');
    }

    public function flashSaleProducts(): HasMany
    {
        return $this->hasMany(FlashSaleProduct::class);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_products')
            ->withPivot('exclude')
            ->withTimestamps();
    }

    public function wishLists(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wish_lists', 'product_id', 'user_id')
            ->withTimestamps();
    }

    public function media()
    {
        return $this->morphToMany(Media::class, 'entity', 'entity_media')
            ->withPivot('zone')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->media()->wherePivot('zone', 'images');
    }

    public function baseImage()
    {
        return $this->media()->wherePivot('zone', 'base_image');
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVendorApproved($query)
    {
        return $query->where('vendor_status', 'approved');
    }

    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeFeatured($query)
    {
        return $query->whereNotNull('new_from')
            ->whereNotNull('new_to')
            ->where('new_from', '<=', now())
            ->where('new_to', '>=', now());
    }

    public function getSellingPriceAttribute()
    {
        if ($this->hasSpecialPrice()) {
            return $this->getSpecialPrice();
        }

        return $this->price;
    }

    public function hasSpecialPrice(): bool
    {
        if (is_null($this->special_price)) {
            return false;
        }

        $now = now();

        if ($this->special_price_start && $this->special_price_start > $now) {
            return false;
        }

        if ($this->special_price_end && $this->special_price_end < $now) {
            return false;
        }

        return true;
    }

    public function getSpecialPrice()
    {
        if (! $this->hasSpecialPrice()) {
            return $this->price;
        }

        if ($this->special_price_type === 'percent') {
            return $this->price - ($this->price * $this->special_price / 100);
        }

        return $this->special_price;
    }

    public function getDiscountPercentage(): float
    {
        if (! $this->hasSpecialPrice() || $this->price <= 0) {
            return 0;
        }

        return round((($this->price - $this->getSpecialPrice()) / $this->price) * 100, 2);
    }

    public function isInStock(): bool
    {
        if (! $this->manage_stock) {
            return $this->in_stock;
        }

        return $this->qty > 0;
    }

    public function incrementViews()
    {
        $this->increment('viewed');
    }

    public function averageRating(): float
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    public function totalReviews(): int
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    public function isNew(): bool
    {
        $now = now();

        return $this->new_from && $this->new_to &&
               $this->new_from <= $now &&
               $this->new_to >= $now;
    }

    public function isVendorApproved(): bool
    {
        return $this->vendor_status === 'approved';
    }

    public function isPendingApproval(): bool
    {
        return $this->vendor_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->vendor_status === 'rejected';
    }
}

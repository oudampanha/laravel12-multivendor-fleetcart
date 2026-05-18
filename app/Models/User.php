<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'phone_no',
        'password',
        'last_login',
        'is_verified',
        'avatar',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['full_name', 'avatar_url', 'profile_image_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'status' => 'boolean',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * Get the vendor profile for the user.
     */
    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    /**
     * Get the roles for the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    /**
     * Get the direct permissions for the user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')->withTimestamps();
    }

    /**
     * Get the OTP verifications for the user.
     */
    public function otpVerifications(): HasMany
    {
        return $this->hasMany(OtpVerification::class, 'email', 'email');
    }

    /**
     * Get the orders for the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get the reviews written by the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Get the vendor reviews written by the user.
     */
    public function vendorReviews(): HasMany
    {
        return $this->hasMany(VendorReview::class, 'customer_id');
    }

    /**
     * Get the addresses for the user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'customer_id');
    }

    /**
     * Get the default address for the user.
     */
    public function defaultAddress(): HasOne
    {
        return $this->hasOne(DefaultAddress::class, 'customer_id');
    }

    /**
     * Get the wish list items for the user.
     */
    public function wishlistItems(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wish_lists')->withTimestamps();
    }

    /**
     * Get the blog posts written by the user.
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Get the media uploaded by the user.
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Get the activations for the user.
     */
    public function activations(): HasMany
    {
        return $this->hasMany(Activation::class);
    }

    /**
     * Get the persistences for the user.
     */
    public function persistences(): HasMany
    {
        return $this->hasMany(Persistence::class);
    }

    /**
     * Get the reminders for the user.
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    /**
     * Get the throttle entries for the user.
     */
    public function throttle(): HasMany
    {
        return $this->hasMany(Throttle::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('title', $role)->exists();
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('title', $permission)->exists();
    }

    /**
     * Check if the user is a vendor.
     */
    public function isVendor(): bool
    {
        return $this->vendor()->exists();
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the avatar URL attribute - Fixed logic
     */
    public function getAvatarUrlAttribute(): ?string
    {
        // If no avatar is set, return default image
        if (empty($this->avatar)) {
            return asset('assets/images/no_image.png');
        }

        // If avatar starts with http:// or https://, it's already a full URL
        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        // If it's a relative path, check if file exists and return storage URL
        if (Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }

        // If file doesn't exist, return default image
        return asset('assets/images/no_image.png');
    }

    /**
     * Get the profile image URL attribute - Alias for avatar_url for consistency
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        return $this->avatar_url;
    }
}

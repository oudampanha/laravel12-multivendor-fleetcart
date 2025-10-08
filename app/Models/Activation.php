<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Activation extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('completed', false);
    }

    public function scopeExpired($query, int $hoursValid = 24)
    {
        return $query->where('created_at', '<', now()->subHours($hoursValid));
    }

    public function scopeValid($query, int $hoursValid = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hoursValid))
            ->where('completed', false);
    }

    public static function createForUser(User $user): self
    {
        // Remove any existing pending activations for this user
        static::forUser($user->id)->pending()->delete();

        return static::create([
            'user_id' => $user->id,
            'code' => static::generateCode(),
            'completed' => false,
        ]);
    }

    public static function findByCode(string $code): ?self
    {
        return static::byCode($code)->first();
    }

    public static function activate(string $code): bool
    {
        $activation = static::byCode($code)
            ->pending()
            ->valid()
            ->first();

        if (! $activation) {
            return false;
        }

        $activation->complete();

        return true;
    }

    public static function activateUser(User $user, string $code): bool
    {
        $activation = static::forUser($user->id)
            ->byCode($code)
            ->pending()
            ->valid()
            ->first();

        if (! $activation) {
            return false;
        }

        $activation->complete();

        return true;
    }

    public function complete(): void
    {
        $this->update([
            'completed' => true,
            'completed_at' => now(),
        ]);

        // Mark user as verified if not already
        if ($this->user && ! $this->user->is_verified) {
            $this->user->update([
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);
        }
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function isPending(): bool
    {
        return ! $this->completed;
    }

    public function isExpired(int $hoursValid = 24): bool
    {
        return $this->created_at < now()->subHours($hoursValid);
    }

    public function isValid(int $hoursValid = 24): bool
    {
        return ! $this->isCompleted() && ! $this->isExpired($hoursValid);
    }

    public function getExpiresAt(int $hoursValid = 24): \Carbon\Carbon
    {
        return $this->created_at->addHours($hoursValid);
    }

    public function getRemainingTime(int $hoursValid = 24): \Carbon\CarbonInterval
    {
        $expiresAt = $this->getExpiresAt($hoursValid);

        return now() < $expiresAt
            ? now()->diffAsCarbonInterval($expiresAt)
            : \Carbon\CarbonInterval::seconds(0);
    }

    public function getActivationUrl(): string
    {
        return route('auth.activate', ['code' => $this->code]);
    }

    public static function cleanupExpired(int $hoursValid = 24): int
    {
        return static::expired($hoursValid)->delete();
    }

    public static function resendForUser(User $user): self
    {
        // Remove existing pending activations
        static::forUser($user->id)->pending()->delete();

        // Create new activation
        return static::createForUser($user);
    }

    public static function hasValidActivation(User $user): bool
    {
        return static::forUser($user->id)
            ->valid()
            ->exists();
    }

    public static function getValidActivation(User $user): ?self
    {
        return static::forUser($user->id)
            ->valid()
            ->first();
    }

    protected static function generateCode(): string
    {
        do {
            $code = Str::random(64);
        } while (static::byCode($code)->exists());

        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activation) {
            if (empty($activation->code)) {
                $activation->code = static::generateCode();
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Persistence extends Model
{
    protected $fillable = [
        'user_id',
        'code',
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

    public function scopeExpired($query, int $daysValid = 30)
    {
        return $query->where('updated_at', '<', now()->subDays($daysValid));
    }

    public function scopeValid($query, int $daysValid = 30)
    {
        return $query->where('updated_at', '>=', now()->subDays($daysValid));
    }

    public static function createForUser(User $user): self
    {
        return static::create([
            'user_id' => $user->id,
            'code' => static::generateCode(),
        ]);
    }

    public static function findByCode(string $code): ?self
    {
        return static::byCode($code)->valid()->first();
    }

    public static function findValidByCode(string $code): ?self
    {
        return static::byCode($code)->valid()->first();
    }

    public static function authenticateByCode(string $code): ?User
    {
        $persistence = static::findValidByCode($code);

        if (!$persistence) {
            return null;
        }

        // Touch the persistence record to extend its lifetime
        $persistence->touch();

        return $persistence->user;
    }

    public static function loginUser(User $user, bool $remember = true): ?string
    {
        if (!$remember) {
            return null;
        }

        // Remove old persistence codes for this user
        static::forUser($user->id)->delete();

        // Create new persistence
        $persistence = static::createForUser($user);

        return $persistence->code;
    }

    public static function logoutUser(?User $user = null, ?string $code = null): bool
    {
        if ($user) {
            return static::forUser($user->id)->delete() > 0;
        }

        if ($code) {
            return static::byCode($code)->delete() > 0;
        }

        return false;
    }

    public static function logoutAllSessions(User $user): int
    {
        return static::forUser($user->id)->delete();
    }

    public function refresh(): void
    {
        $this->touch();
    }

    public function isExpired(int $daysValid = 30): bool
    {
        return $this->updated_at < now()->subDays($daysValid);
    }

    public function isValid(int $daysValid = 30): bool
    {
        return !$this->isExpired($daysValid);
    }

    public function getExpiresAt(int $daysValid = 30): \Carbon\Carbon
    {
        return $this->updated_at->addDays($daysValid);
    }

    public function getRemainingTime(int $daysValid = 30): \Carbon\CarbonInterval
    {
        $expiresAt = $this->getExpiresAt($daysValid);
        
        return now() < $expiresAt 
            ? now()->diffAsCarbonInterval($expiresAt)
            : \Carbon\CarbonInterval::seconds(0);
    }

    public static function cleanupExpired(int $daysValid = 30): int
    {
        return static::expired($daysValid)->delete();
    }

    public static function getUserSessions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return static::forUser($user->id)
            ->valid()
            ->latest()
            ->get();
    }

    public static function getSessionsCount(User $user): int
    {
        return static::forUser($user->id)->valid()->count();
    }

    public static function hasValidSession(User $user): bool
    {
        return static::forUser($user->id)->valid()->exists();
    }

    public function getSessionInfo(): array
    {
        return [
            'code' => $this->code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'expires_at' => $this->getExpiresAt(),
            'is_expired' => $this->isExpired(),
            'remaining_time' => $this->getRemainingTime(),
        ];
    }

    protected static function generateCode(): string
    {
        do {
            $code = Str::random(128);
        } while (static::byCode($code)->exists());

        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($persistence) {
            if (empty($persistence->code)) {
                $persistence->code = static::generateCode();
            }
        });
    }
}
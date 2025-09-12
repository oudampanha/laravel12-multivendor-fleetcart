<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reminder extends Model
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

    public function scopeExpired($query, int $hoursValid = 2)
    {
        return $query->where('created_at', '<', now()->subHours($hoursValid));
    }

    public function scopeValid($query, int $hoursValid = 2)
    {
        return $query->where('created_at', '>=', now()->subHours($hoursValid))
                    ->where('completed', false);
    }

    public static function createForUser(User $user): self
    {
        // Remove any existing pending reminders for this user
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

    public static function findValidByCode(string $code): ?self
    {
        return static::byCode($code)->valid()->first();
    }

    public static function findForUserByCode(User $user, string $code): ?self
    {
        return static::forUser($user->id)
            ->byCode($code)
            ->valid()
            ->first();
    }

    public static function verify(string $code): ?self
    {
        return static::findValidByCode($code);
    }

    public static function verifyForUser(User $user, string $code): ?self
    {
        return static::findForUserByCode($user, $code);
    }

    public function complete(): void
    {
        $this->update([
            'completed' => true,
            'completed_at' => now(),
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function isPending(): bool
    {
        return !$this->completed;
    }

    public function isExpired(int $hoursValid = 2): bool
    {
        return $this->created_at < now()->subHours($hoursValid);
    }

    public function isValid(int $hoursValid = 2): bool
    {
        return !$this->isCompleted() && !$this->isExpired($hoursValid);
    }

    public function getExpiresAt(int $hoursValid = 2): \Carbon\Carbon
    {
        return $this->created_at->addHours($hoursValid);
    }

    public function getRemainingTime(int $hoursValid = 2): \Carbon\CarbonInterval
    {
        $expiresAt = $this->getExpiresAt($hoursValid);
        
        return now() < $expiresAt 
            ? now()->diffAsCarbonInterval($expiresAt)
            : \Carbon\CarbonInterval::seconds(0);
    }

    public function getResetUrl(): string
    {
        return route('password.reset', ['token' => $this->code]);
    }

    public static function cleanupExpired(int $hoursValid = 2): int
    {
        return static::expired($hoursValid)->delete();
    }

    public static function cleanupCompleted(int $daysOld = 7): int
    {
        return static::completed()
            ->where('completed_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    public static function resendForUser(User $user): self
    {
        // Remove existing pending reminders
        static::forUser($user->id)->pending()->delete();

        // Create new reminder
        return static::createForUser($user);
    }

    public static function hasValidReminder(User $user): bool
    {
        return static::forUser($user->id)
            ->valid()
            ->exists();
    }

    public static function getValidReminder(User $user): ?self
    {
        return static::forUser($user->id)
            ->valid()
            ->first();
    }

    public static function canCreateReminder(User $user, int $cooldownMinutes = 5): bool
    {
        $lastReminder = static::forUser($user->id)
            ->latest()
            ->first();

        if (!$lastReminder) {
            return true;
        }

        return $lastReminder->created_at < now()->subMinutes($cooldownMinutes);
    }

    public static function getRemainingCooldown(User $user, int $cooldownMinutes = 5): int
    {
        $lastReminder = static::forUser($user->id)
            ->latest()
            ->first();

        if (!$lastReminder) {
            return 0;
        }

        $cooldownEnd = $lastReminder->created_at->addMinutes($cooldownMinutes);
        
        return now() < $cooldownEnd 
            ? now()->diffInSeconds($cooldownEnd)
            : 0;
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

        static::creating(function ($reminder) {
            if (empty($reminder->code)) {
                $reminder->code = static::generateCode();
            }
        });
    }
}
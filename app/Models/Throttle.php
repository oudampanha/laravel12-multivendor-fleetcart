<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Throttle extends Model
{
    protected $table = 'throttle';

    protected $fillable = [
        'user_id',
        'type',
        'ip',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForIp($query, string $ip)
    {
        return $query->where('ip', $ip);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeGlobal($query)
    {
        return $query->byType('global');
    }

    public function scopeUser($query)
    {
        return $query->byType('user');
    }

    public function scopeIp($query)
    {
        return $query->byType('ip');
    }

    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    public function scopeExpired($query, int $minutes = 60)
    {
        return $query->where('created_at', '<', now()->subMinutes($minutes));
    }

    public static function recordAttempt(?User $user = null, string $type = 'global', ?string $ip = null): self
    {
        return static::create([
            'user_id' => $user?->id,
            'type' => $type,
            'ip' => $ip ?? request()->ip(),
        ]);
    }

    public static function recordGlobalAttempt(?string $ip = null): self
    {
        return static::recordAttempt(null, 'global', $ip);
    }

    public static function recordUserAttempt(User $user, ?string $ip = null): self
    {
        return static::recordAttempt($user, 'user', $ip);
    }

    public static function recordIpAttempt(string $ip): self
    {
        return static::recordAttempt(null, 'ip', $ip);
    }

    public static function getGlobalAttempts(int $minutes = 60, ?string $ip = null): int
    {
        $query = static::global()->recent($minutes);

        if ($ip) {
            $query->forIp($ip);
        }

        return $query->count();
    }

    public static function getUserAttempts(User $user, int $minutes = 60): int
    {
        return static::forUser($user->id)
            ->user()
            ->recent($minutes)
            ->count();
    }

    public static function getIpAttempts(string $ip, int $minutes = 60): int
    {
        return static::forIp($ip)
            ->ip()
            ->recent($minutes)
            ->count();
    }

    public static function isGlobalThrottled(int $limit = 100, int $minutes = 60, ?string $ip = null): bool
    {
        return static::getGlobalAttempts($minutes, $ip) >= $limit;
    }

    public static function isUserThrottled(User $user, int $limit = 5, int $minutes = 60): bool
    {
        return static::getUserAttempts($user, $minutes) >= $limit;
    }

    public static function isIpThrottled(string $ip, int $limit = 20, int $minutes = 60): bool
    {
        return static::getIpAttempts($ip, $minutes) >= $limit;
    }

    public static function checkLoginThrottle(?User $user = null, ?string $ip = null): array
    {
        $ip = $ip ?? request()->ip();
        
        // Check global throttling (by IP)
        $globalAttempts = static::getGlobalAttempts(60, $ip);
        $globalLimit = 50; // 50 attempts per hour per IP
        
        if ($globalAttempts >= $globalLimit) {
            return [
                'throttled' => true,
                'type' => 'global',
                'attempts' => $globalAttempts,
                'limit' => $globalLimit,
                'reset_time' => static::getResetTime('global', $ip),
            ];
        }

        // Check IP-specific throttling
        $ipAttempts = static::getIpAttempts($ip, 15);
        $ipLimit = 10; // 10 attempts per 15 minutes per IP
        
        if ($ipAttempts >= $ipLimit) {
            return [
                'throttled' => true,
                'type' => 'ip',
                'attempts' => $ipAttempts,
                'limit' => $ipLimit,
                'reset_time' => static::getResetTime('ip', $ip, 15),
            ];
        }

        // Check user-specific throttling
        if ($user) {
            $userAttempts = static::getUserAttempts($user, 30);
            $userLimit = 5; // 5 attempts per 30 minutes per user
            
            if ($userAttempts >= $userLimit) {
                return [
                    'throttled' => true,
                    'type' => 'user',
                    'attempts' => $userAttempts,
                    'limit' => $userLimit,
                    'reset_time' => static::getResetTime('user', $user->id, 30),
                ];
            }
        }

        return [
            'throttled' => false,
            'global_attempts' => $globalAttempts,
            'ip_attempts' => $ipAttempts,
            'user_attempts' => $user ? static::getUserAttempts($user, 30) : 0,
        ];
    }

    public static function getResetTime(string $type, $identifier = null, int $minutes = 60): \Carbon\Carbon
    {
        $query = static::byType($type)->recent($minutes);

        switch ($type) {
            case 'global':
                if ($identifier) {
                    $query->forIp($identifier);
                }
                break;
            case 'user':
                $query->forUser($identifier);
                break;
            case 'ip':
                $query->forIp($identifier);
                break;
        }

        $oldestAttempt = $query->oldest()->first();
        
        return $oldestAttempt 
            ? $oldestAttempt->created_at->addMinutes($minutes)
            : now();
    }

    public static function clearExpired(int $minutes = 60): int
    {
        return static::expired($minutes)->delete();
    }

    public static function clearForUser(User $user): int
    {
        return static::forUser($user->id)->delete();
    }

    public static function clearForIp(string $ip): int
    {
        return static::forIp($ip)->delete();
    }

    public static function clearAll(): int
    {
        return static::query()->delete();
    }

    public static function getThrottleStats(int $minutes = 60): array
    {
        $recent = static::recent($minutes);

        return [
            'total_attempts' => $recent->count(),
            'global_attempts' => $recent->global()->count(),
            'user_attempts' => $recent->user()->count(),
            'ip_attempts' => $recent->ip()->count(),
            'unique_ips' => $recent->distinct('ip')->count('ip'),
            'unique_users' => $recent->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];
    }

    public static function getTopThrottledIps(int $limit = 10, int $minutes = 60): \Illuminate\Database\Eloquent\Collection
    {
        return static::recent($minutes)
            ->selectRaw('ip, COUNT(*) as attempt_count')
            ->groupBy('ip')
            ->orderByDesc('attempt_count')
            ->limit($limit)
            ->get();
    }

    public static function getTopThrottledUsers(int $limit = 10, int $minutes = 60): \Illuminate\Database\Eloquent\Collection
    {
        return static::recent($minutes)
            ->whereNotNull('user_id')
            ->selectRaw('user_id, COUNT(*) as attempt_count')
            ->with('user:id,email,first_name,last_name')
            ->groupBy('user_id')
            ->orderByDesc('attempt_count')
            ->limit($limit)
            ->get();
    }

    public function isExpired(int $minutes = 60): bool
    {
        return $this->created_at < now()->subMinutes($minutes);
    }

    public function getRemainingTime(int $minutes = 60): int
    {
        $resetTime = $this->created_at->addMinutes($minutes);
        
        return now() < $resetTime 
            ? now()->diffInSeconds($resetTime)
            : 0;
    }
}
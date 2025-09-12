<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SearchTerm extends Model
{
    protected $fillable = [
        'term',
        'results',
        'hits',
    ];

    protected $casts = [
        'results' => 'integer',
        'hits' => 'integer',
    ];

    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderByDesc('hits')->limit($limit);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    public function scopeWithResults($query)
    {
        return $query->where('results', '>', 0);
    }

    public function scopeWithoutResults($query)
    {
        return $query->where('results', 0);
    }

    public function scopeByTerm($query, string $term)
    {
        return $query->where('term', $this->normalizeTerm($term));
    }

    public static function recordSearch(string $term, int $results = 0): self
    {
        $normalizedTerm = static::normalizeTerm($term);

        if (empty($normalizedTerm)) {
            return new static();
        }

        $searchTerm = static::firstOrCreate(
            ['term' => $normalizedTerm],
            ['results' => $results, 'hits' => 0]
        );

        // Increment hits and update results
        $searchTerm->increment('hits');
        
        if ($results > 0) {
            $searchTerm->update(['results' => $results]);
        }

        return $searchTerm;
    }

    public static function getPopularTerms(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return static::popular($limit)
            ->withResults()
            ->get();
    }

    public static function getRecentTerms(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return static::recent($limit)
            ->withResults()
            ->get();
    }

    public static function getTermsWithoutResults(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return static::withoutResults()
            ->orderByDesc('hits')
            ->limit($limit)
            ->get();
    }

    public static function getSuggestions(string $term, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        $normalizedTerm = static::normalizeTerm($term);

        if (empty($normalizedTerm)) {
            return collect();
        }

        return static::where('term', 'LIKE', "%{$normalizedTerm}%")
            ->withResults()
            ->orderByDesc('hits')
            ->limit($limit)
            ->get();
    }

    public static function getRelatedTerms(string $term, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        $normalizedTerm = static::normalizeTerm($term);
        $words = explode(' ', $normalizedTerm);

        if (empty($words)) {
            return collect();
        }

        $query = static::withResults();

        foreach ($words as $word) {
            if (strlen($word) >= 3) { // Only use words with 3+ characters
                $query->orWhere('term', 'LIKE', "%{$word}%");
            }
        }

        return $query->where('term', '!=', $normalizedTerm)
            ->orderByDesc('hits')
            ->limit($limit)
            ->get();
    }

    public static function cleanupOldTerms(int $daysOld = 90, int $minHits = 1): int
    {
        return static::where('updated_at', '<', now()->subDays($daysOld))
            ->where('hits', '<', $minHits)
            ->delete();
    }

    public static function getSearchStats(): array
    {
        return [
            'total_terms' => static::count(),
            'total_searches' => static::sum('hits'),
            'terms_with_results' => static::withResults()->count(),
            'terms_without_results' => static::withoutResults()->count(),
            'avg_results_per_term' => static::avg('results'),
            'avg_hits_per_term' => static::avg('hits'),
        ];
    }

    public static function getTopTermsByPeriod(string $period = 'month', int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $dateColumn = 'updated_at';
        
        switch ($period) {
            case 'day':
                $date = now()->startOfDay();
                break;
            case 'week':
                $date = now()->startOfWeek();
                break;
            case 'month':
                $date = now()->startOfMonth();
                break;
            case 'year':
                $date = now()->startOfYear();
                break;
            default:
                $date = now()->startOfMonth();
        }

        return static::where($dateColumn, '>=', $date)
            ->withResults()
            ->orderByDesc('hits')
            ->limit($limit)
            ->get();
    }

    protected static function normalizeTerm(string $term): string
    {
        // Remove extra whitespace and convert to lowercase
        $normalized = trim(strtolower($term));
        
        // Remove special characters except spaces, hyphens, and apostrophes
        $normalized = preg_replace('/[^\w\s\-\']/u', '', $normalized);
        
        // Replace multiple spaces with single space
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return $normalized;
    }

    public function hasResults(): bool
    {
        return $this->results > 0;
    }

    public function isPopular(int $threshold = 10): bool
    {
        return $this->hits >= $threshold;
    }

    public function getFormattedTerm(): string
    {
        return Str::title($this->term);
    }

    public function getSearchUrl(): string
    {
        return route('products.search', ['query' => $this->term]);
    }

    public function incrementHits(): void
    {
        $this->increment('hits');
        $this->touch(); // Update the updated_at timestamp
    }

    public function updateResults(int $results): void
    {
        $this->update(['results' => $results]);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CurrencyRate extends Model
{
    protected $fillable = [
        'currency',
        'rate',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
    ];

    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    public function scopeActive($query)
    {
        return $query->where('rate', '>', 0);
    }

    public static function getRate(string $currency): float
    {
        return Cache::remember("currency_rate.{$currency}", 3600, function () use ($currency) {
            $currencyRate = static::byCurrency($currency)->first();
            
            return $currencyRate ? (float) $currencyRate->rate : 1.0;
        });
    }

    public static function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $fromRate = static::getRate($fromCurrency);
        $toRate = static::getRate($toCurrency);

        // Convert to base currency first, then to target currency
        $baseAmount = $amount / $fromRate;
        
        return $baseAmount * $toRate;
    }

    public static function setRate(string $currency, float $rate): void
    {
        static::updateOrCreate(
            ['currency' => $currency],
            ['rate' => $rate]
        );

        // Clear cache
        Cache::forget("currency_rate.{$currency}");
        Cache::forget('all_currency_rates');
    }

    public static function getAllRates(): array
    {
        return Cache::remember('all_currency_rates', 3600, function () {
            return static::pluck('rate', 'currency')->toArray();
        });
    }

    public static function getSupportedCurrencies(): array
    {
        return Cache::remember('supported_currencies', 3600, function () {
            return static::active()->pluck('currency')->toArray();
        });
    }

    public static function isSupportedCurrency(string $currency): bool
    {
        return in_array($currency, static::getSupportedCurrencies());
    }

    public static function getBaseCurrency(): string
    {
        return Setting::get('base_currency', 'USD');
    }

    public static function formatAmount(float $amount, string $currency, ?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        
        return $formatter->formatCurrency($amount, $currency);
    }

    public static function convertAndFormat(float $amount, string $fromCurrency, string $toCurrency, ?string $locale = null): string
    {
        $convertedAmount = static::convert($amount, $fromCurrency, $toCurrency);
        
        return static::formatAmount($convertedAmount, $toCurrency, $locale);
    }

    public static function clearCache(): void
    {
        Cache::forget('all_currency_rates');
        Cache::forget('supported_currencies');
        
        $currencies = static::pluck('currency');
        foreach ($currencies as $currency) {
            Cache::forget("currency_rate.{$currency}");
        }
    }

    public function isBaseCurrency(): bool
    {
        return $this->currency === static::getBaseCurrency();
    }

    public function getFormattedRate(): string
    {
        return number_format($this->rate, 8);
    }

    public function getCurrencySymbol(): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'INR' => '₹',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'Fr',
            'SEK' => 'kr',
            'NOK' => 'kr',
            'DKK' => 'kr',
            'PLN' => 'zł',
            'CZK' => 'Kč',
            'HUF' => 'Ft',
            'RUB' => '₽',
            'BRL' => 'R$',
            'MXN' => 'Mex$',
            'SGD' => 'S$',
            'HKD' => 'HK$',
            'KRW' => '₩',
            'THB' => '฿',
            'MYR' => 'RM',
            'PHP' => '₱',
            'IDR' => 'Rp',
            'VND' => '₫',
        ];

        return $symbols[$this->currency] ?? $this->currency;
    }
}
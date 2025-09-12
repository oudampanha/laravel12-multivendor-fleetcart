<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxClass extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'based_on',
    ];

    public function taxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', TaxClass::class);
    }

    public function calculateTax($amount, $country = null, $state = null, $city = null, $zip = null)
    {
        $totalTax = 0;
        
        $rates = $this->taxRates()
            ->when($country, fn($query) => $query->where('country', $country))
            ->when($state, fn($query) => $query->where('state', $state))
            ->when($city, fn($query) => $query->where('city', $city))
            ->when($zip, fn($query) => $query->where('zip', $zip))
            ->orderBy('position')
            ->get();

        foreach ($rates as $rate) {
            $totalTax += ($amount * $rate->rate) / 100;
        }

        return $totalTax;
    }
}
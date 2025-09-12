<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tax_class_id',
        'country',
        'state',
        'city',
        'zip',
        'rate',
        'position',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'position' => 'integer',
    ];

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_taxes')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', TaxRate::class);
    }

    public function scopeForLocation($query, $country = null, $state = null, $city = null, $zip = null)
    {
        return $query->when($country, fn($q) => $q->where('country', $country))
                    ->when($state, fn($q) => $q->where('state', $state))
                    ->when($city, fn($q) => $q->where('city', $city))
                    ->when($zip, fn($q) => $q->where('zip', $zip));
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    public function calculateTax($amount)
    {
        return ($amount * $this->rate) / 100;
    }
}
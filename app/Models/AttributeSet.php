<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeSet extends Model
{
    use HasTranslations;

    protected $fillable = [];
    
    protected array $translatable = ['name'];

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    public function getTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translatable_id')
            ->where('translatable_type', AttributeSet::class);
    }
}
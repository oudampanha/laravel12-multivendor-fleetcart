<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EntityMedia extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entity_media';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_id',
        'entity_type',
        'entity_id',
        'zone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_id' => 'integer',
        'entity_id' => 'integer',
        'zone' => 'string',
        'entity_type' => 'string',
    ];

    /**
     * Get the file associated with this entity media.
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'file_id');
    }

    /**
     * Get the owning entity model (product, category, etc.).
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }
}
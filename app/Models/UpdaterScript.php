<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdaterScript extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'updater_scripts';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'script',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'script' => 'string',
    ];
}

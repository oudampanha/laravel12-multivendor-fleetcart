<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDownload extends Model
{
    protected $fillable = [
        'order_id',
        'file_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'file_id');
    }
}

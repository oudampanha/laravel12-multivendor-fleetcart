<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    public const UPDATED_AT = null;

    public const TYPE_OPENING = 'opening';

    public const TYPE_RECEIPT = 'receipt';

    public const TYPE_ISSUE = 'issue';

    public const TYPE_ADJUSTMENT_IN = 'adjustment_in';

    public const TYPE_ADJUSTMENT_OUT = 'adjustment_out';

    public const TYPE_TRANSFER_IN = 'transfer_in';

    public const TYPE_TRANSFER_OUT = 'transfer_out';

    public const TYPE_SALE = 'sale';

    public const TYPE_RETURN = 'return';

    public const TYPE_RESERVATION = 'reservation';

    public const TYPE_RELEASE = 'release';

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'product_variant_id',
        'type',
        'reference_type',
        'reference_id',
        'quantity',
        'balance_after',
        'unit_cost',
        'total_cost',
        'batch_number',
        'expiry_date',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'balance_after' => 'integer',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:4',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeByOrder($query, int $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByTransactionId($query, string $transactionId)
    {
        return $query->where('transaction_id', $transactionId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getAmount(): float
    {
        return $this->order ? $this->order->total : 0;
    }

    public function getCurrency(): string
    {
        return $this->order ? $this->order->currency : 'USD';
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->getAmount(), 2).' '.$this->getCurrency();
    }

    public function isSuccessful(): bool
    {
        return ! is_null($this->transaction_id);
    }

    public static function createForOrder(Order $order, string $transactionId, string $paymentMethod): self
    {
        return static::create([
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'payment_method' => $paymentMethod,
        ]);
    }

    public static function findByTransactionId(string $transactionId): ?self
    {
        return static::where('transaction_id', $transactionId)->first();
    }
}

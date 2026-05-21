<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    /**
     * Apply a positive stock movement (receipt-like) and update balances.
     */
    public function receive(
        int $warehouseId,
        int $productId,
        ?int $productVariantId,
        int $quantity,
        float $unitCost = 0,
        string $type = StockMovement::TYPE_RECEIPT,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $batchNumber = null,
        ?string $expiryDate = null,
        ?string $notes = null,
    ): StockMovement {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Receive quantity must be positive.');
        }

        return DB::transaction(function () use (
            $warehouseId, $productId, $productVariantId, $quantity, $unitCost,
            $type, $referenceType, $referenceId, $batchNumber, $expiryDate, $notes,
        ) {
            $stock = $this->lockStock($warehouseId, $productId, $productVariantId);

            // Weighted average cost
            if ($unitCost > 0) {
                $oldValue = $stock->average_cost * max(0, $stock->quantity);
                $newValue = $unitCost * $quantity;
                $totalQty = max(0, $stock->quantity) + $quantity;
                $stock->average_cost = $totalQty > 0 ? ($oldValue + $newValue) / $totalQty : $unitCost;
            }

            $stock->quantity += $quantity;
            $stock->last_movement_at = now();
            $stock->save();

            $movement = StockMovement::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $productVariantId,
                'type' => $type,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'quantity' => $quantity,
                'balance_after' => $stock->quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $unitCost * $quantity,
                'batch_number' => $batchNumber,
                'expiry_date' => $expiryDate,
                'notes' => $notes,
                'user_id' => Auth::id(),
            ]);

            $this->syncAggregates($productId);

            return $movement;
        });
    }

    /**
     * Apply a negative stock movement (issue-like) and update balances.
     */
    public function issue(
        int $warehouseId,
        int $productId,
        ?int $productVariantId,
        int $quantity,
        float $unitCost = 0,
        string $type = StockMovement::TYPE_ISSUE,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
    ): StockMovement {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Issue quantity must be positive.');
        }

        return DB::transaction(function () use (
            $warehouseId, $productId, $productVariantId, $quantity, $unitCost,
            $type, $referenceType, $referenceId, $notes,
        ) {
            $stock = $this->lockStock($warehouseId, $productId, $productVariantId);

            $cost = $unitCost > 0 ? $unitCost : (float) $stock->average_cost;

            $stock->quantity -= $quantity;
            $stock->last_movement_at = now();
            $stock->save();

            $movement = StockMovement::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $productVariantId,
                'type' => $type,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'quantity' => -$quantity,
                'balance_after' => $stock->quantity,
                'unit_cost' => $cost,
                'total_cost' => $cost * $quantity,
                'notes' => $notes,
                'user_id' => Auth::id(),
            ]);

            $this->syncAggregates($productId);

            return $movement;
        });
    }

    /**
     * Adjust to an absolute quantity. Writes a single signed movement of the difference.
     */
    public function adjust(
        int $warehouseId,
        int $productId,
        ?int $productVariantId,
        int $actualQuantity,
        float $unitCost = 0,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
    ): ?StockMovement {
        return DB::transaction(function () use (
            $warehouseId, $productId, $productVariantId, $actualQuantity,
            $unitCost, $referenceType, $referenceId, $notes,
        ) {
            $stock = $this->lockStock($warehouseId, $productId, $productVariantId);
            $diff = $actualQuantity - $stock->quantity;

            if ($diff === 0) {
                return null;
            }

            $type = $diff > 0 ? StockMovement::TYPE_ADJUSTMENT_IN : StockMovement::TYPE_ADJUSTMENT_OUT;
            $cost = $unitCost > 0 ? $unitCost : (float) $stock->average_cost;

            $stock->quantity = $actualQuantity;
            $stock->last_movement_at = now();
            $stock->save();

            $movement = StockMovement::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $productVariantId,
                'type' => $type,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'quantity' => $diff,
                'balance_after' => $stock->quantity,
                'unit_cost' => $cost,
                'total_cost' => $cost * abs($diff),
                'notes' => $notes,
                'user_id' => Auth::id(),
            ]);

            $this->syncAggregates($productId);

            return $movement;
        });
    }

    /**
     * Get or create a stock row with a row-level lock for safe concurrent updates.
     */
    protected function lockStock(int $warehouseId, int $productId, ?int $productVariantId): ProductStock
    {
        $stock = ProductStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->where('product_variant_id', $productVariantId)
            ->lockForUpdate()
            ->first();

        if (! $stock) {
            $stock = ProductStock::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $productVariantId,
                'quantity' => 0,
                'reserved_quantity' => 0,
            ]);
        }

        return $stock;
    }

    /**
     * Update legacy aggregate columns on products.qty / products.in_stock so old
     * code paths that ignore the new tables still see correct totals.
     */
    public function syncAggregates(int $productId): void
    {
        $totalQty = (int) ProductStock::where('product_id', $productId)->sum('quantity');

        Product::where('id', $productId)->update([
            'qty' => $totalQty,
            'in_stock' => $totalQty > 0,
        ]);
    }
}

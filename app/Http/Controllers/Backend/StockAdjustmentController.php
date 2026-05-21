<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends BaseController
{
    protected string $resource = 'stock_adjustment';

    protected array $additionalPermissions = ['inventory_management_access'];

    public function index(Request $request)
    {
        $query = StockAdjustment::with(['warehouse', 'creator', 'poster'])
            ->withCount('items');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($warehouseId = $request->input('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($reason = $request->input('reason')) {
            $query->where('reason', $reason);
        }

        $adjustments = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $warehouses = Warehouse::active()->orderBy('position')->get();

        return view('admin.stock_adjustments.index', compact('adjustments', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::active()->orderBy('position')->get();
        $products = Product::orderBy('id', 'desc')->limit(500)->get();

        return view('admin.stock_adjustments.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_date' => 'required|date',
            'reason' => 'required|in:'.implode(',', StockAdjustment::REASONS),
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.actual_quantity' => 'required|integer|min:0',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $adjustment = DB::transaction(function () use ($data) {
            $adjustment = StockAdjustment::create([
                'code' => $this->nextCode(),
                'warehouse_id' => $data['warehouse_id'],
                'adjustment_date' => $data['adjustment_date'],
                'reason' => $data['reason'],
                'status' => StockAdjustment::STATUS_DRAFT,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                $systemQty = (int) ProductStock::where('warehouse_id', $adjustment->warehouse_id)
                    ->where('product_id', $item['product_id'])
                    ->where('product_variant_id', $item['product_variant_id'] ?? null)
                    ->value('quantity');

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'system_quantity' => $systemQty,
                    'actual_quantity' => $item['actual_quantity'],
                    'difference' => $item['actual_quantity'] - $systemQty,
                    'unit_cost' => $item['unit_cost'] ?? 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $adjustment;
        });

        return redirect()->route('admin.stock-adjustments.show', $adjustment)->with('success', 'Stock adjustment created. Review and post when ready.');
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['warehouse', 'creator', 'poster', 'items.product', 'items.variant']);

        return view('admin.stock_adjustments.show', compact('stockAdjustment'));
    }

    public function edit(StockAdjustment $stockAdjustment)
    {
        abort_unless($stockAdjustment->isDraft(), 403, 'Cannot edit a posted adjustment.');

        $stockAdjustment->load('items.product', 'items.variant');
        $warehouses = Warehouse::active()->orderBy('position')->get();
        $products = Product::orderBy('id', 'desc')->limit(500)->get();

        return view('admin.stock_adjustments.edit', compact('stockAdjustment', 'warehouses', 'products'));
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        abort_unless($stockAdjustment->isDraft(), 403, 'Cannot edit a posted adjustment.');

        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_date' => 'required|date',
            'reason' => 'required|in:'.implode(',', StockAdjustment::REASONS),
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.actual_quantity' => 'required|integer|min:0',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($stockAdjustment, $data) {
            $stockAdjustment->update([
                'warehouse_id' => $data['warehouse_id'],
                'adjustment_date' => $data['adjustment_date'],
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
            ]);
            $stockAdjustment->items()->delete();

            foreach ($data['items'] as $item) {
                $systemQty = (int) ProductStock::where('warehouse_id', $stockAdjustment->warehouse_id)
                    ->where('product_id', $item['product_id'])
                    ->where('product_variant_id', $item['product_variant_id'] ?? null)
                    ->value('quantity');

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $stockAdjustment->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'system_quantity' => $systemQty,
                    'actual_quantity' => $item['actual_quantity'],
                    'difference' => $item['actual_quantity'] - $systemQty,
                    'unit_cost' => $item['unit_cost'] ?? 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)->with('success', 'Stock adjustment updated.');
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        abort_unless($stockAdjustment->isDraft(), 403, 'Cannot delete a posted adjustment.');

        $stockAdjustment->delete();

        return redirect()->route('admin.stock-adjustments.index')->with('success', 'Adjustment deleted.');
    }

    public function post(StockAdjustment $stockAdjustment, StockService $stockService)
    {
        abort_unless($stockAdjustment->isDraft(), 403, 'Adjustment is not in draft status.');

        DB::transaction(function () use ($stockAdjustment, $stockService) {
            foreach ($stockAdjustment->items as $item) {
                $stockService->adjust(
                    warehouseId: $stockAdjustment->warehouse_id,
                    productId: $item->product_id,
                    productVariantId: $item->product_variant_id,
                    actualQuantity: $item->actual_quantity,
                    unitCost: (float) $item->unit_cost,
                    referenceType: StockAdjustment::class,
                    referenceId: $stockAdjustment->id,
                    notes: $stockAdjustment->reason.($item->notes ? ' - '.$item->notes : ''),
                );
            }

            $stockAdjustment->update([
                'status' => StockAdjustment::STATUS_POSTED,
                'posted_by' => Auth::id(),
                'posted_at' => now(),
            ]);
        });

        return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)->with('success', 'Adjustment posted to stock.');
    }

    public function cancel(StockAdjustment $stockAdjustment)
    {
        abort_unless($stockAdjustment->isDraft(), 403, 'Only draft adjustments can be cancelled.');

        $stockAdjustment->update(['status' => StockAdjustment::STATUS_CANCELLED]);

        return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)->with('success', 'Adjustment cancelled.');
    }

    protected function nextCode(): string
    {
        $next = (StockAdjustment::withTrashed()->max('id') ?? 0) + 1;

        return 'ADJ-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}

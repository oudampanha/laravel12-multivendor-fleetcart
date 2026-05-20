<?php

namespace App\Http\Controllers\Backend;

use App\Models\ProductStock;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockTake;
use App\Models\StockTakeItem;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTakeController extends BaseController
{
    protected string $resource = 'stock_take';

    protected array $additionalPermissions = ['inventory_management_access'];

    public function index(Request $request)
    {
        $query = StockTake::with(['warehouse', 'creator', 'completer'])->withCount('items');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $stockTakes = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('admin.stock_takes.index', compact('stockTakes'));
    }

    public function create()
    {
        $warehouses = Warehouse::active()->orderBy('position')->get();

        return view('admin.stock_takes.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'count_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $stockTake = DB::transaction(function () use ($data) {
            $stockTake = StockTake::create([
                'code' => $this->nextCode(),
                'warehouse_id' => $data['warehouse_id'],
                'count_date' => $data['count_date'],
                'status' => StockTake::STATUS_DRAFT,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $stocks = ProductStock::where('warehouse_id', $stockTake->warehouse_id)->get();
            foreach ($stocks as $stock) {
                StockTakeItem::create([
                    'stock_take_id' => $stockTake->id,
                    'product_id' => $stock->product_id,
                    'product_variant_id' => $stock->product_variant_id,
                    'expected_quantity' => $stock->quantity,
                    'counted_quantity' => null,
                    'difference' => null,
                    'unit_cost' => $stock->average_cost,
                ]);
            }

            return $stockTake;
        });

        return redirect()->route('admin.stock-takes.show', $stockTake)->with('success', 'Stock take created with '.$stockTake->items()->count().' items.');
    }

    public function show(StockTake $stockTake)
    {
        $stockTake->load(['warehouse', 'creator', 'completer', 'items.product', 'items.variant']);

        return view('admin.stock_takes.show', compact('stockTake'));
    }

    public function edit(StockTake $stockTake)
    {
        abort_if($stockTake->isCompleted(), 403, 'Cannot edit a completed stock take.');

        $stockTake->load('items.product', 'items.variant', 'warehouse');

        return view('admin.stock_takes.edit', compact('stockTake'));
    }

    public function update(Request $request, StockTake $stockTake)
    {
        abort_if($stockTake->isCompleted(), 403, 'Cannot edit a completed stock take.');

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:stock_take_items,id',
            'items.*.counted_quantity' => 'nullable|integer|min:0',
            'items.*.notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($stockTake, $data) {
            foreach ($data['items'] as $line) {
                $item = $stockTake->items()->findOrFail($line['id']);
                $counted = $line['counted_quantity'] ?? null;
                $item->update([
                    'counted_quantity' => $counted,
                    'difference' => $counted !== null ? $counted - $item->expected_quantity : null,
                    'notes' => $line['notes'] ?? null,
                ]);
            }

            $stockTake->update([
                'status' => StockTake::STATUS_IN_PROGRESS,
                'notes' => $data['notes'] ?? $stockTake->notes,
            ]);
        });

        return redirect()->route('admin.stock-takes.show', $stockTake)->with('success', 'Stock take updated.');
    }

    public function destroy(StockTake $stockTake)
    {
        abort_if($stockTake->isCompleted(), 403, 'Cannot delete a completed stock take.');

        $stockTake->delete();

        return redirect()->route('admin.stock-takes.index')->with('success', 'Stock take deleted.');
    }

    public function complete(StockTake $stockTake, StockService $stockService)
    {
        abort_if($stockTake->isCompleted(), 403, 'Stock take already completed.');

        DB::transaction(function () use ($stockTake, $stockService) {
            $itemsWithDifference = $stockTake->items()
                ->whereNotNull('counted_quantity')
                ->where('difference', '!=', 0)
                ->get();

            if ($itemsWithDifference->isNotEmpty()) {
                $adjustment = StockAdjustment::create([
                    'code' => 'ADJ-'.((StockAdjustment::withTrashed()->max('id') ?? 0) + 1),
                    'warehouse_id' => $stockTake->warehouse_id,
                    'adjustment_date' => now()->toDateString(),
                    'reason' => 'recount',
                    'status' => StockAdjustment::STATUS_POSTED,
                    'notes' => 'Auto-generated from stock take '.$stockTake->code,
                    'created_by' => Auth::id(),
                    'posted_by' => Auth::id(),
                    'posted_at' => now(),
                ]);

                foreach ($itemsWithDifference as $item) {
                    StockAdjustmentItem::create([
                        'stock_adjustment_id' => $adjustment->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'system_quantity' => $item->expected_quantity,
                        'actual_quantity' => $item->counted_quantity,
                        'difference' => $item->difference,
                        'unit_cost' => $item->unit_cost,
                    ]);

                    $stockService->adjust(
                        warehouseId: $stockTake->warehouse_id,
                        productId: $item->product_id,
                        productVariantId: $item->product_variant_id,
                        actualQuantity: $item->counted_quantity,
                        unitCost: (float) $item->unit_cost,
                        referenceType: StockTake::class,
                        referenceId: $stockTake->id,
                        notes: 'Cycle count '.$stockTake->code,
                    );
                }
            }

            $stockTake->update([
                'status' => StockTake::STATUS_COMPLETED,
                'completed_by' => Auth::id(),
                'completed_at' => now(),
            ]);
        });

        return redirect()->route('admin.stock-takes.show', $stockTake)->with('success', 'Stock take completed and stock adjusted.');
    }

    public function cancel(StockTake $stockTake)
    {
        abort_if($stockTake->isCompleted(), 403, 'Cannot cancel a completed stock take.');

        $stockTake->update(['status' => StockTake::STATUS_CANCELLED]);

        return redirect()->route('admin.stock-takes.show', $stockTake)->with('success', 'Stock take cancelled.');
    }

    protected function nextCode(): string
    {
        $next = (StockTake::withTrashed()->max('id') ?? 0) + 1;

        return 'CC-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferController extends BaseController
{
    protected string $resource = 'stock_transfer';

    protected array $additionalPermissions = ['inventory_management_access'];

    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'creator'])->withCount('items');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $transfers = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('admin.stock_transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::active()->orderBy('position')->get();
        $products = Product::orderBy('id', 'desc')->limit(500)->get();

        return view('admin.stock_transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTransfer($request);

        $transfer = DB::transaction(function () use ($data) {
            $transfer = StockTransfer::create([
                'code' => $this->nextCode(),
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id' => $data['to_warehouse_id'],
                'transfer_date' => $data['transfer_date'],
                'status' => StockTransfer::STATUS_DRAFT,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);
            $this->syncItems($transfer, $data['items']);

            return $transfer;
        });

        return redirect()->route('admin.stock-transfers.show', $transfer)->with('success', 'Transfer created.');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['fromWarehouse', 'toWarehouse', 'creator', 'shipper', 'receiver', 'items.product', 'items.variant']);

        return view('admin.stock_transfers.show', compact('stockTransfer'));
    }

    public function edit(StockTransfer $stockTransfer)
    {
        abort_unless($stockTransfer->isDraft(), 403, 'Cannot edit a non-draft transfer.');

        $stockTransfer->load('items.product', 'items.variant');
        $warehouses = Warehouse::active()->orderBy('position')->get();
        $products = Product::orderBy('id', 'desc')->limit(500)->get();

        return view('admin.stock_transfers.edit', compact('stockTransfer', 'warehouses', 'products'));
    }

    public function update(Request $request, StockTransfer $stockTransfer)
    {
        abort_unless($stockTransfer->isDraft(), 403, 'Cannot edit a non-draft transfer.');

        $data = $this->validateTransfer($request);

        DB::transaction(function () use ($stockTransfer, $data) {
            $stockTransfer->update([
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id' => $data['to_warehouse_id'],
                'transfer_date' => $data['transfer_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            $stockTransfer->items()->delete();
            $this->syncItems($stockTransfer, $data['items']);
        });

        return redirect()->route('admin.stock-transfers.show', $stockTransfer)->with('success', 'Transfer updated.');
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        abort_unless($stockTransfer->isDraft(), 403, 'Only draft transfers can be deleted.');

        $stockTransfer->delete();

        return redirect()->route('admin.stock-transfers.index')->with('success', 'Transfer deleted.');
    }

    public function ship(StockTransfer $stockTransfer, StockService $stockService)
    {
        abort_unless($stockTransfer->isDraft(), 403, 'Only draft transfers can be shipped.');

        DB::transaction(function () use ($stockTransfer, $stockService) {
            foreach ($stockTransfer->items as $item) {
                $stockService->issue(
                    warehouseId: $stockTransfer->from_warehouse_id,
                    productId: $item->product_id,
                    productVariantId: $item->product_variant_id,
                    quantity: $item->quantity_sent,
                    unitCost: (float) $item->unit_cost,
                    type: StockMovement::TYPE_TRANSFER_OUT,
                    referenceType: StockTransfer::class,
                    referenceId: $stockTransfer->id,
                    notes: 'Transfer '.$stockTransfer->code,
                );
            }
            $stockTransfer->update([
                'status' => StockTransfer::STATUS_IN_TRANSIT,
                'shipped_at' => now(),
                'shipped_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.stock-transfers.show', $stockTransfer)->with('success', 'Transfer shipped.');
    }

    public function receive(Request $request, StockTransfer $stockTransfer, StockService $stockService)
    {
        abort_unless($stockTransfer->isInTransit(), 403, 'Only in-transit transfers can be received.');

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:stock_transfer_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($stockTransfer, $stockService, $data) {
            $allFullyReceived = true;
            foreach ($data['items'] as $line) {
                $item = $stockTransfer->items()->findOrFail($line['id']);
                $qty = (int) $line['quantity_received'];
                if ($qty <= 0) {
                    $allFullyReceived = false;

                    continue;
                }

                $stockService->receive(
                    warehouseId: $stockTransfer->to_warehouse_id,
                    productId: $item->product_id,
                    productVariantId: $item->product_variant_id,
                    quantity: $qty,
                    unitCost: (float) $item->unit_cost,
                    type: StockMovement::TYPE_TRANSFER_IN,
                    referenceType: StockTransfer::class,
                    referenceId: $stockTransfer->id,
                    notes: 'Transfer '.$stockTransfer->code,
                );

                $item->update(['quantity_received' => $qty]);
                if ($qty < $item->quantity_sent) {
                    $allFullyReceived = false;
                }
            }

            $stockTransfer->update([
                'status' => $allFullyReceived ? StockTransfer::STATUS_RECEIVED : StockTransfer::STATUS_PARTIAL,
                'received_at' => now(),
                'received_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.stock-transfers.show', $stockTransfer)->with('success', 'Transfer received.');
    }

    public function cancel(StockTransfer $stockTransfer)
    {
        abort_unless($stockTransfer->isDraft(), 403, 'Only draft transfers can be cancelled.');

        $stockTransfer->update(['status' => StockTransfer::STATUS_CANCELLED]);

        return redirect()->route('admin.stock-transfers.show', $stockTransfer)->with('success', 'Transfer cancelled.');
    }

    protected function validateTransfer(Request $request): array
    {
        return $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity_sent' => 'required|integer|min:1',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);
    }

    protected function syncItems(StockTransfer $transfer, array $items): void
    {
        foreach ($items as $item) {
            StockTransferItem::create([
                'stock_transfer_id' => $transfer->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'] ?? null,
                'quantity_sent' => $item['quantity_sent'],
                'unit_cost' => $item['unit_cost'] ?? 0,
                'notes' => $item['notes'] ?? null,
            ]);
        }
    }

    protected function nextCode(): string
    {
        $next = (StockTransfer::withTrashed()->max('id') ?? 0) + 1;

        return 'TRF-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoodsReceiptController extends BaseController
{
    protected string $resource = 'goods_receipt';

    protected array $additionalPermissions = ['inventory_management_access'];

    public function index(Request $request)
    {
        $query = GoodsReceipt::with(['purchaseOrder', 'supplier', 'warehouse', 'creator'])->withCount('items');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $goodsReceipts = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('admin.goods_receipts.index', compact('goodsReceipts'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $warehouses = Warehouse::active()->orderBy('position')->get();
        $products = Product::orderBy('id', 'desc')->limit(500)->get();
        $purchaseOrders = PurchaseOrder::whereIn('status', [
            PurchaseOrder::STATUS_SENT,
            PurchaseOrder::STATUS_CONFIRMED,
            PurchaseOrder::STATUS_PARTIAL,
        ])->orderBy('id', 'desc')->limit(200)->get();

        $purchaseOrder = null;
        if ($poId = $request->input('purchase_order_id')) {
            $purchaseOrder = PurchaseOrder::with('items.product', 'items.variant')->find($poId);
        }

        return view('admin.goods_receipts.create', compact('suppliers', 'warehouses', 'products', 'purchaseOrders', 'purchaseOrder'));
    }

    public function store(Request $request)
    {
        $data = $this->validateGrn($request);

        $grn = DB::transaction(function () use ($data) {
            $grn = GoodsReceipt::create([
                'code' => $this->nextCode(),
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'receipt_date' => $data['receipt_date'],
                'status' => GoodsReceipt::STATUS_DRAFT,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);
            $this->syncItems($grn, $data['items']);

            return $grn;
        });

        return redirect()->route('admin.goods-receipts.show', $grn)->with('success', 'Goods receipt created.');
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load(['purchaseOrder', 'supplier', 'warehouse', 'creator', 'poster', 'items.product', 'items.variant']);

        return view('admin.goods_receipts.show', compact('goodsReceipt'));
    }

    public function edit(GoodsReceipt $goodsReceipt)
    {
        abort_unless($goodsReceipt->isDraft(), 403, 'Cannot edit a posted goods receipt.');

        $goodsReceipt->load('items.product', 'items.variant');
        $suppliers = Supplier::active()->orderBy('name')->get();
        $warehouses = Warehouse::active()->orderBy('position')->get();
        $products = Product::orderBy('id', 'desc')->limit(500)->get();

        return view('admin.goods_receipts.edit', compact('goodsReceipt', 'suppliers', 'warehouses', 'products'));
    }

    public function update(Request $request, GoodsReceipt $goodsReceipt)
    {
        abort_unless($goodsReceipt->isDraft(), 403, 'Cannot edit a posted goods receipt.');

        $data = $this->validateGrn($request);

        DB::transaction(function () use ($goodsReceipt, $data) {
            $goodsReceipt->update([
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'receipt_date' => $data['receipt_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            $goodsReceipt->items()->delete();
            $this->syncItems($goodsReceipt, $data['items']);
        });

        return redirect()->route('admin.goods-receipts.show', $goodsReceipt)->with('success', 'Goods receipt updated.');
    }

    public function destroy(GoodsReceipt $goodsReceipt)
    {
        abort_unless($goodsReceipt->isDraft(), 403, 'Only draft receipts can be deleted.');

        $goodsReceipt->delete();

        return redirect()->route('admin.goods-receipts.index')->with('success', 'Goods receipt deleted.');
    }

    public function post(GoodsReceipt $goodsReceipt, StockService $stockService)
    {
        abort_unless($goodsReceipt->isDraft(), 403, 'Goods receipt already posted or cancelled.');

        DB::transaction(function () use ($goodsReceipt, $stockService) {
            foreach ($goodsReceipt->items as $item) {
                $stockService->receive(
                    warehouseId: $goodsReceipt->warehouse_id,
                    productId: $item->product_id,
                    productVariantId: $item->product_variant_id,
                    quantity: $item->quantity_received,
                    unitCost: (float) $item->unit_cost,
                    referenceType: GoodsReceipt::class,
                    referenceId: $goodsReceipt->id,
                    batchNumber: $item->batch_number,
                    expiryDate: $item->expiry_date?->toDateString(),
                    notes: 'GRN '.$goodsReceipt->code,
                );

                if ($item->purchase_order_item_id) {
                    $poItem = $item->purchaseOrderItem;
                    if ($poItem) {
                        $poItem->increment('quantity_received', $item->quantity_received);
                    }
                }
            }

            $goodsReceipt->update([
                'status' => GoodsReceipt::STATUS_POSTED,
                'posted_by' => Auth::id(),
                'posted_at' => now(),
            ]);

            if ($po = $goodsReceipt->purchaseOrder) {
                $po->load('items');
                $allReceived = $po->items->every(fn ($item) => $item->quantity_received >= $item->quantity_ordered);
                $anyReceived = $po->items->contains(fn ($item) => $item->quantity_received > 0);
                $po->update([
                    'status' => $allReceived
                        ? PurchaseOrder::STATUS_RECEIVED
                        : ($anyReceived ? PurchaseOrder::STATUS_PARTIAL : $po->status),
                    'received_at' => $allReceived ? now() : $po->received_at,
                ]);
            }
        });

        return redirect()->route('admin.goods-receipts.show', $goodsReceipt)->with('success', 'Goods receipt posted to stock.');
    }

    public function cancel(GoodsReceipt $goodsReceipt)
    {
        abort_unless($goodsReceipt->isDraft(), 403, 'Only draft receipts can be cancelled.');

        $goodsReceipt->update(['status' => GoodsReceipt::STATUS_CANCELLED]);

        return redirect()->route('admin.goods-receipts.show', $goodsReceipt)->with('success', 'Goods receipt cancelled.');
    }

    protected function validateGrn(Request $request): array
    {
        return $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:1',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.batch_number' => 'nullable|string|max:100',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.notes' => 'nullable|string',
        ]);
    }

    protected function syncItems(GoodsReceipt $grn, array $items): void
    {
        foreach ($items as $item) {
            GoodsReceiptItem::create([
                'goods_receipt_id' => $grn->id,
                'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'] ?? null,
                'quantity_received' => $item['quantity_received'],
                'unit_cost' => $item['unit_cost'] ?? 0,
                'batch_number' => $item['batch_number'] ?? null,
                'expiry_date' => $item['expiry_date'] ?? null,
                'notes' => $item['notes'] ?? null,
            ]);
        }
    }

    protected function nextCode(): string
    {
        $next = (GoodsReceipt::withTrashed()->max('id') ?? 0) + 1;

        return 'GRN-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}

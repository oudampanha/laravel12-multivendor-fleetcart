<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends BaseController
{
    protected string $resource = 'purchase_order';

    protected array $additionalPermissions = ['inventory_management_access'];

    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'warehouse', 'creator'])->withCount('items');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($supplierId = $request->input('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        if ($warehouseId = $request->input('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        $purchaseOrders = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $suppliers = Supplier::active()->orderBy('name')->get();
        $warehouses = Warehouse::active()->orderBy('position')->get();

        return view('admin.purchase_orders.index', compact('purchaseOrders', 'suppliers', 'warehouses'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $warehouses = Warehouse::active()->orderBy('position')->get();
        $products = Product::orderBy('id', 'desc')->limit(500)->get();

        return view('admin.purchase_orders.create', compact('suppliers', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePo($request);

        $po = DB::transaction(function () use ($data) {
            $po = PurchaseOrder::create([
                'code' => $this->nextCode(),
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'order_date' => $data['order_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'status' => PurchaseOrder::STATUS_DRAFT,
                'currency_code' => $data['currency_code'] ?? 'USD',
                'exchange_rate' => $data['exchange_rate'] ?? 1,
                'shipping_amount' => $data['shipping_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'created_by' => Auth::id(),
            ]);
            $this->syncItems($po, $data['items']);
            $this->recalculateTotals($po);

            return $po;
        });

        return redirect()->route('admin.purchase-orders.show', $po)->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'warehouse', 'creator', 'approver', 'items.product', 'items.variant', 'goodsReceipts']);

        return view('admin.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->isEditable(), 403, 'Cannot edit this purchase order.');

        $purchaseOrder->load('items.product', 'items.variant');
        $suppliers = Supplier::active()->orderBy('name')->get();
        $warehouses = Warehouse::active()->orderBy('position')->get();
        $products = Product::orderBy('id', 'desc')->limit(500)->get();

        return view('admin.purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'warehouses', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->isEditable(), 403, 'Cannot edit this purchase order.');

        $data = $this->validatePo($request);

        DB::transaction(function () use ($purchaseOrder, $data) {
            $purchaseOrder->update([
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'order_date' => $data['order_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'currency_code' => $data['currency_code'] ?? 'USD',
                'exchange_rate' => $data['exchange_rate'] ?? 1,
                'shipping_amount' => $data['shipping_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
            ]);
            $purchaseOrder->items()->delete();
            $this->syncItems($purchaseOrder, $data['items']);
            $this->recalculateTotals($purchaseOrder);
        });

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)->with('success', 'Purchase order updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->status === PurchaseOrder::STATUS_DRAFT, 403, 'Only draft POs can be deleted.');

        $purchaseOrder->delete();

        return redirect()->route('admin.purchase-orders.index')->with('success', 'Purchase order deleted.');
    }

    public function send(PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->status === PurchaseOrder::STATUS_DRAFT, 403, 'Only draft POs can be sent.');

        $purchaseOrder->update(['status' => PurchaseOrder::STATUS_SENT]);

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)->with('success', 'Purchase order sent.');
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        abort_unless(in_array($purchaseOrder->status, [PurchaseOrder::STATUS_DRAFT, PurchaseOrder::STATUS_SENT]), 403, 'Cannot approve this purchase order.');

        $purchaseOrder->update([
            'status' => PurchaseOrder::STATUS_CONFIRMED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)->with('success', 'Purchase order approved.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->status === PurchaseOrder::STATUS_RECEIVED, 403, 'Received POs cannot be cancelled.');

        $purchaseOrder->update(['status' => PurchaseOrder::STATUS_CANCELLED]);

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)->with('success', 'Purchase order cancelled.');
    }

    protected function validatePo(Request $request): array
    {
        return $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'currency_code' => 'nullable|string|size:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);
    }

    protected function syncItems(PurchaseOrder $po, array $items): void
    {
        foreach ($items as $item) {
            $qty = (int) $item['quantity_ordered'];
            $cost = (float) $item['unit_cost'];
            $taxRate = (float) ($item['tax_rate'] ?? 0);
            $discount = (float) ($item['discount'] ?? 0);
            $lineTotal = ($qty * $cost) - $discount;
            $lineTotal += $lineTotal * $taxRate / 100;

            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'] ?? null,
                'quantity_ordered' => $qty,
                'quantity_received' => 0,
                'unit_cost' => $cost,
                'tax_rate' => $taxRate,
                'discount' => $discount,
                'line_total' => $lineTotal,
                'notes' => $item['notes'] ?? null,
            ]);
        }
    }

    protected function recalculateTotals(PurchaseOrder $po): void
    {
        $items = $po->items()->get();
        $subtotal = $items->sum(fn ($item) => $item->quantity_ordered * $item->unit_cost - $item->discount);
        $taxAmount = $items->sum(fn ($item) => (($item->quantity_ordered * $item->unit_cost - $item->discount) * $item->tax_rate) / 100);
        $total = $subtotal + $taxAmount + (float) $po->shipping_amount - (float) $po->discount_amount;

        $po->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
        ]);
    }

    protected function nextCode(): string
    {
        $next = (PurchaseOrder::withTrashed()->max('id') ?? 0) + 1;

        return 'PO-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}

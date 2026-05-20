@csrf
<div class="row">
  <div class="col-md-3"><div class="form-group">
    <label>Purchase Order</label>
    <select name="purchase_order_id" class="form-control">
      <option value="">— None (manual receipt) —</option>
      @foreach($purchaseOrders ?? [] as $po)
        <option value="{{ $po->id }}" @selected(old('purchase_order_id', $goodsReceipt->purchase_order_id ?? (optional($purchaseOrder ?? null)->id)) == $po->id)>{{ $po->code }} — {{ optional($po->supplier)->name }}</option>
      @endforeach
    </select>
  </div></div>
  <div class="col-md-3"><div class="form-group">
    <label>Supplier <span class="text-danger">*</span></label>
    <select name="supplier_id" class="form-control" required>
      <option value="">Select supplier</option>
      @foreach($suppliers as $s)
        <option value="{{ $s->id }}" @selected(old('supplier_id', $goodsReceipt->supplier_id ?? (optional($purchaseOrder ?? null)->supplier_id)) == $s->id)>{{ $s->name }}</option>
      @endforeach
    </select>
  </div></div>
  <div class="col-md-3"><div class="form-group">
    <label>Warehouse <span class="text-danger">*</span></label>
    <select name="warehouse_id" class="form-control" required>
      <option value="">Select warehouse</option>
      @foreach($warehouses as $w)
        <option value="{{ $w->id }}" @selected(old('warehouse_id', $goodsReceipt->warehouse_id ?? (optional($purchaseOrder ?? null)->warehouse_id)) == $w->id)>{{ $w->name }}</option>
      @endforeach
    </select>
  </div></div>
  <div class="col-md-3"><div class="form-group">
    <label>Receipt Date <span class="text-danger">*</span></label>
    <input type="date" name="receipt_date" class="form-control" value="{{ old('receipt_date', optional($goodsReceipt->receipt_date ?? null)->format('Y-m-d') ?? now()->toDateString()) }}" required>
  </div></div>
</div>
<div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $goodsReceipt->notes ?? '') }}</textarea></div>

<h5 class="mt-4">Items</h5>
<div class="table-responsive">
  <table class="table table-sm" id="itemsTable">
    <thead><tr><th>Product <span class="text-danger">*</span></th><th>Variant</th><th>PO Item ID</th><th>Qty Received <span class="text-danger">*</span></th><th>Unit Cost</th><th>Batch</th><th>Expiry</th><th>Notes</th><th></th></tr></thead>
    <tbody>
      @php
        if (isset($goodsReceipt)) {
            $items = old('items', $goodsReceipt->items->toArray());
        } elseif (isset($purchaseOrder) && $purchaseOrder) {
            $items = old('items', $purchaseOrder->items->map(fn ($it) => [
                'product_id' => $it->product_id,
                'product_variant_id' => $it->product_variant_id,
                'purchase_order_item_id' => $it->id,
                'quantity_received' => max(0, $it->quantity_ordered - $it->quantity_received),
                'unit_cost' => $it->unit_cost,
            ])->all());
        } else {
            $items = old('items', [['product_id' => null]]);
        }
      @endphp
      @foreach($items as $i => $item)
        <tr>
          <td>
            <select name="items[{{ $i }}][product_id]" class="form-control" required>
              <option value="">Select product</option>
              @foreach($products as $p)
                <option value="{{ $p->id }}" @selected(($item['product_id'] ?? null) == $p->id)>{{ $p->name ?? $p->slug }}</option>
              @endforeach
            </select>
          </td>
          <td><input type="number" name="items[{{ $i }}][product_variant_id]" value="{{ $item['product_variant_id'] ?? '' }}" class="form-control" placeholder="Variant ID"></td>
          <td><input type="number" name="items[{{ $i }}][purchase_order_item_id]" value="{{ $item['purchase_order_item_id'] ?? '' }}" class="form-control" placeholder="optional"></td>
          <td><input type="number" name="items[{{ $i }}][quantity_received]" value="{{ $item['quantity_received'] ?? 1 }}" class="form-control" min="1" required></td>
          <td><input type="number" name="items[{{ $i }}][unit_cost]" value="{{ $item['unit_cost'] ?? 0 }}" class="form-control" step="0.0001" min="0"></td>
          <td><input type="text" name="items[{{ $i }}][batch_number]" value="{{ $item['batch_number'] ?? '' }}" class="form-control"></td>
          <td><input type="date" name="items[{{ $i }}][expiry_date]" value="{{ $item['expiry_date'] ?? '' }}" class="form-control"></td>
          <td><input type="text" name="items[{{ $i }}][notes]" value="{{ $item['notes'] ?? '' }}" class="form-control"></td>
          <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<button type="button" class="btn btn-sm btn-outline-primary" id="addItem"><i class="fas fa-plus"></i> Add line</button>

@push('scripts')
<script>
document.getElementById('addItem')?.addEventListener('click', () => {
  const tbody = document.querySelector('#itemsTable tbody');
  const i = tbody.querySelectorAll('tr').length;
  const tr = tbody.querySelector('tr');
  if (!tr) return;
  const clone = tr.cloneNode(true);
  clone.querySelectorAll('select, input').forEach(el => {
    el.name = el.name.replace(/items\[\d+\]/, `items[${i}]`);
    if (el.tagName === 'SELECT') el.value = ''; else el.value = el.type === 'number' ? 0 : '';
  });
  tbody.appendChild(clone);
});
</script>
@endpush

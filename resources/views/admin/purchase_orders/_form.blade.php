@csrf
<div class="row">
  <div class="col-md-4"><div class="form-group">
    <label>Supplier <span class="text-danger">*</span></label>
    <select name="supplier_id" class="form-control" required>
      <option value="">Select supplier</option>
      @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id', $purchaseOrder->supplier_id ?? null) == $s->id)>{{ $s->name }}</option>@endforeach
    </select>
  </div></div>
  <div class="col-md-4"><div class="form-group">
    <label>Warehouse <span class="text-danger">*</span></label>
    <select name="warehouse_id" class="form-control" required>
      <option value="">Select warehouse</option>
      @foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(old('warehouse_id', $purchaseOrder->warehouse_id ?? null) == $w->id)>{{ $w->name }}</option>@endforeach
    </select>
  </div></div>
  <div class="col-md-2"><div class="form-group">
    <label>Order Date <span class="text-danger">*</span></label>
    <input type="date" name="order_date" class="form-control" value="{{ old('order_date', optional($purchaseOrder->order_date ?? null)->format('Y-m-d') ?? now()->toDateString()) }}" required>
  </div></div>
  <div class="col-md-2"><div class="form-group">
    <label>Expected Date</label>
    <input type="date" name="expected_date" class="form-control" value="{{ old('expected_date', optional($purchaseOrder->expected_date ?? null)->format('Y-m-d')) }}">
  </div></div>
</div>
<div class="row">
  <div class="col-md-2"><div class="form-group"><label>Currency</label><input type="text" name="currency_code" value="{{ old('currency_code', $purchaseOrder->currency_code ?? 'USD') }}" class="form-control" maxlength="3"></div></div>
  <div class="col-md-2"><div class="form-group"><label>Exchange Rate</label><input type="number" name="exchange_rate" value="{{ old('exchange_rate', $purchaseOrder->exchange_rate ?? 1) }}" class="form-control" step="0.0001" min="0"></div></div>
  <div class="col-md-2"><div class="form-group"><label>Shipping</label><input type="number" name="shipping_amount" value="{{ old('shipping_amount', $purchaseOrder->shipping_amount ?? 0) }}" class="form-control" step="0.01" min="0"></div></div>
  <div class="col-md-2"><div class="form-group"><label>Discount</label><input type="number" name="discount_amount" value="{{ old('discount_amount', $purchaseOrder->discount_amount ?? 0) }}" class="form-control" step="0.01" min="0"></div></div>
</div>
<div class="row">
  <div class="col-md-6"><div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $purchaseOrder->notes ?? '') }}</textarea></div></div>
  <div class="col-md-6"><div class="form-group"><label>Terms</label><textarea name="terms" class="form-control" rows="2">{{ old('terms', $purchaseOrder->terms ?? '') }}</textarea></div></div>
</div>

<h5 class="mt-4">Items</h5>
<div class="table-responsive">
  <table class="table table-sm" id="itemsTable">
    <thead><tr><th>Product <span class="text-danger">*</span></th><th>Variant</th><th>Qty <span class="text-danger">*</span></th><th>Unit Cost <span class="text-danger">*</span></th><th>Tax %</th><th>Discount</th><th>Notes</th><th></th></tr></thead>
    <tbody>
      @php $items = old('items', isset($purchaseOrder) ? $purchaseOrder->items->toArray() : [['product_id' => null]]); @endphp
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
          <td><input type="number" name="items[{{ $i }}][quantity_ordered]" value="{{ $item['quantity_ordered'] ?? 1 }}" class="form-control" min="1" required></td>
          <td><input type="number" name="items[{{ $i }}][unit_cost]" value="{{ $item['unit_cost'] ?? 0 }}" class="form-control" step="0.0001" min="0" required></td>
          <td><input type="number" name="items[{{ $i }}][tax_rate]" value="{{ $item['tax_rate'] ?? 0 }}" class="form-control" step="0.01" min="0" max="100"></td>
          <td><input type="number" name="items[{{ $i }}][discount]" value="{{ $item['discount'] ?? 0 }}" class="form-control" step="0.01" min="0"></td>
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
